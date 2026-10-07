<?php

  // Parts of a hierarchy: how each part adds up to the whole.
  //
  //   {chart 'treemap',  data='budget', label='post', value='amount', levels='department'}
  //   {chart 'sunburst', data='sales',  label='product', value='total', levels='region, country'}
  //
  // label= names the parts - else the first field that is no number - and value= their
  // size, else the first numeric field; a part of nothing or less is left out. levels= lists
  // the fields of the levels above the parts, the outermost first; parts with the same
  // path add up. A treemap divides its rectangle in nested rectangles (squarified, so they
  // stay near square), a sunburst its circle in rings, the inner ring the outermost level.
  // The colour is that of the outermost group, eight colours and the rest grey; a treemap
  // without levels= is one colour, a sunburst without one colours each part.

  function padChartHierarchy ( $kind, $rows, $width, $height ) {

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $groups = padChartFields ( padTagParm ( 'levels' ) );
    $used   = $groups;
    $lField = padChartPick ( 'label', $names,   $used );
    $vField = padChartPick ( 'value', $numbers, $used );

    $title = (string) padTagParm ( 'title', ucfirst ( $vField !== '' ? $vField : 'value' ) . ( $groups ? ' by ' . implode ( ' and ', $groups ) : ( $lField !== '' ? " by $lField" : '' ) ) );

    $root = [ 'name' => '', 'value' => 0, 'children' => [] ];

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $v   = $row [$vField] ?? NULL;

      if ( ! padChartFinite ( $v ) or $v <= 0 )
        continue;

      $path = [];
      foreach ( $groups as $group )
        $path [] = padChartText ( $row, $group );
      $path [] = padChartText ( $row, $lField );

      $node = &$root;
      $node ['value'] += $v;

      foreach ( $path as $name ) {
        if ( ! isset ( $node ['children'] [$name] ) )
          $node ['children'] [$name] = [ 'name' => (string) $name, 'value' => 0, 'children' => [] ];
        $node = &$node ['children'] [$name];
        $node ['value'] += $v;
      }

      unset ( $node );

    }

    if ( ! $root ['children'] or ! is_finite ( $root ['value'] ) )
      return '';

    $desc = [];
    padChartHierarchyDesc ( $root, [], $desc );

    list ( $id, $svg ) = padChartOpen ( $kind, $root, $desc, $title, $width, $height );

    if ( $kind == 'sunburst' )
      return $svg . padChartSunburst ( $root, count ( $groups ) + 1, $width, $height ) . '</svg>';

    return $svg . padChartTreemap ( $root, 0, 0, 0, $width, $height, 0 ) . '</svg>';

  }

  // The parts with their path, for the <desc>.

  function padChartHierarchyDesc ( $node, $path, &$desc ) {

    foreach ( $node ['children'] as $child )
      if ( $child ['children'] )
        padChartHierarchyDesc ( $child, array_merge ( $path, [ $child ['name'] ] ), $desc );
      else
        $desc [] = implode ( ' › ', array_merge ( $path, [ $child ['name'] ] ) ) . ': ' . padChartNumber ( $child ['value'] );

  }

  // The rectangles of a treemap, level by level: the children of a node squarified in its
  // rectangle, a group inset by a line of the surface around it. A part with room for it
  // is named, and given its value under the name when there is room for that too.

  function padChartTreemap ( $node, $depth, $x, $y, $w, $h, $slot, $path = [] ) {

    $svg      = '';
    $children = array_values ( $node ['children'] );
    $rects    = padChartSquarify ( array_column ( $children, 'value' ), $x, $y, $w, $h );

    foreach ( $children as $i => $child ) {

      list ( $cx, $cy, $cw, $ch ) = $rects [$i];

      $class = $depth ? $slot : ( $child ['children'] ? padChartSlot ( $i ) : 'pc-c1' );
      $trail = array_merge ( $path, [ $child ['name'] ] );

      if ( $child ['children'] ) {
        $svg .= padChartTreemap ( $child, $depth + 1, $cx + 1, $cy + 1, max ( 0, $cw - 2 ), max ( 0, $ch - 2 ), $class, $trail )
              . '<rect class="pc-frame" x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy ) . '" width="' . padChartXY ( $cw ) . '" height="' . padChartXY ( $ch ) . '"/>';
        continue;
      }

      $svg .= "<rect class=\"pc-mark pc-gap $class\" x=\"" . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy ) . '" width="' . padChartXY ( $cw ) . '" height="' . padChartXY ( $ch ) . '"><title>'
            . padChartAttr ( implode ( ' › ', $trail ) . ': ' . padChartNumber ( $child ['value'] ) ) . '</title></rect>';

      if ( $cw >= 40 and $ch >= 24 ) {
        $svg .= '<text class="pc-in" x="' . padChartXY ( $cx + 5 ) . '" y="' . padChartXY ( $cy + 15 ) . '" pointer-events="none">' . padChartAttr ( padChartFit ( $child ['name'], $cw - 10 ) ) . '</text>';
        if ( $ch >= 38 )
          $svg .= '<text class="pc-in" x="' . padChartXY ( $cx + 5 ) . '" y="' . padChartXY ( $cy + 29 ) . '" pointer-events="none">' . padChartAttr ( padChartFit ( padChartNumber ( $child ['value'] ), $cw - 10 ) ) . '</text>';
      }

    }

    return $svg;

  }

  // The squarified layout (Bruls, Huizing and van Wijk): the values, largest first, laid
  // in rows along the shorter side of what is left, a row closed when the next value would
  // make its rectangles less square. Answers [ x, y, width, height ] per value, in the
  // order of the values.

  function padChartSquarify ( $values, $x, $y, $w, $h ) {

    $total = array_sum ( $values );
    $rects = [];

    if ( $total <= 0 or $w <= 0 or $h <= 0 ) {
      foreach ( $values as $i => $value )
        $rects [$i] = [ $x, $y, 0, 0 ];
      return $rects;
    }

    $areas = [];
    foreach ( $values as $i => $value )
      $areas [$i] = $value / $total * $w * $h;

    arsort ( $areas );

    $worst = function ( $row, $side ) {
      $sum = array_sum ( $row );
      if ( $sum <= 0 or $side <= 0 )
        return INF;
      return max ( $side * $side * max ( $row ) / ( $sum * $sum ), $sum * $sum / ( $side * $side * min ( $row ) ) );
    };

    $row  = [];
    $keys = array_keys ( $areas );

    while ( $keys or $row ) {

      $side = min ( $w, $h );
      $key  = $keys [0] ?? NULL;

      if ( $key !== NULL and ( ! $row or $worst ( array_merge ( $row, [ $key => $areas [$key] ] ), $side ) <= $worst ( $row, $side ) ) ) {
        $row [$key] = $areas [$key];
        array_shift ( $keys );
        continue;
      }

      // Lay the row: along the left side when the space is wide, along the top when tall.

      $sum = array_sum ( $row );

      if ( $w >= $h ) {
        $thick = ( $h > 0 ) ? $sum / $h : 0;
        $at    = $y;
        foreach ( $row as $i => $area ) {
          $length    = ( $thick > 0 ) ? $area / $thick : 0;
          $rects [$i] = [ $x, $at, $thick, $length ];
          $at       += $length;
        }
        $x += $thick;
        $w -= $thick;
      } else {
        $thick = ( $w > 0 ) ? $sum / $w : 0;
        $at    = $x;
        foreach ( $row as $i => $area ) {
          $length    = ( $thick > 0 ) ? $area / $thick : 0;
          $rects [$i] = [ $at, $y, $length, $thick ];
          $at       += $length;
        }
        $y += $thick;
        $h -= $thick;
      }

      $row = [];

    }

    return $rects;

  }

  // The rings of a sunburst: the root an empty middle with the total in it, each level a
  // ring, a node's angle its share of its parent's. A deeper level is drawn lighter in the
  // colour of its outermost group.

  function padChartSunburst ( $root, $levels, $width, $height ) {

    $radius = max ( 10, min ( $width, $height ) / 2 - 6 );
    $cx     = $width / 2;
    $cy     = $height / 2;
    $hole   = $radius * 0.28;
    $ring   = ( $radius - $hole ) / $levels;

    $svg   = '';
    $stack = [];
    $a     = 0;

    foreach ( array_values ( $root ['children'] ) as $i => $child ) {
      $sweep   = $child ['value'] / $root ['value'] * 2 * M_PI;
      $stack[] = [ $child, 0, $a, $a + $sweep, padChartSlot ( $i ), [ $child ['name'] ] ];
      $a      += $sweep;
    }

    while ( $stack ) {

      list ( $node, $depth, $a0, $a1, $slot, $path ) = array_shift ( $stack );

      $d     = padChartArc ( $cx, $cy, $hole + $depth * $ring, $hole + ( $depth + 1 ) * $ring, $a0, $a1 );
      $light = ( $depth > 0 ) ? ' pc-dp' . min ( 3, $depth ) : '';

      $svg .= "<path class=\"pc-mark pc-seg $slot$light\" fill-rule=\"evenodd\" d=\"$d\"><title>"
            . padChartAttr ( implode ( ' › ', $path ) . ': ' . padChartNumber ( $node ['value'] ) ) . '</title></path>';

      $at = $a0;

      foreach ( $node ['children'] as $child ) {
        $sweep   = ( $node ['value'] > 0 ) ? $child ['value'] / $node ['value'] * ( $a1 - $a0 ) : 0;
        $stack[] = [ $child, $depth + 1, $at, $at + $sweep, $slot, array_merge ( $path, [ $child ['name'] ] ) ];
        $at     += $sweep;
      }

    }

    return $svg . '<text class="pc-big" x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy + 6 ) . '" text-anchor="middle">' . padChartNumber ( $root ['value'] ) . '</text>';

  }

?>

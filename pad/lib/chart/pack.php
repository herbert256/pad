<?php

  // Parts of a hierarchy as circles in circles: {chart 'pack', data='files', levels='folder',
  // label='file', value='kb'}.
  //
  // The rows and options of the treemap: label= names the parts - else the first field
  // that is no number - and value= their size, else the first numeric field; a part of
  // nothing or less is left out. levels= lists the fields of the levels above the parts,
  // the outermost first; parts with the same path add up. A part is a circle with the area
  // of its value; the parts of a group are packed against each other, largest first, and
  // the group is the smallest circle around them, drawn light. The colour is that of the
  // outermost group, eight colours and the rest grey, named in a legend; without levels=
  // the parts are one colour. A part with room for its name is named, and given its value too.

  function padChartPack ( $rows, $width, $height ) {

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $groups = padChartFields ( padTagParm ( 'levels' ) );
    $used   = $groups;
    $lField = padChartPick ( 'label', $names,   $used );
    $vField = padChartPick ( 'value', $numbers, $used );

    $title = (string) padTagParm ( 'title', ucfirst ( $vField !== '' ? $vField : 'value' ) . ( $groups ? ' by ' . implode ( ' and ', $groups ) : ( $lField !== '' ? " by $lField" : '' ) ) );

    $root = padChartPackTree ( $rows, $groups, $lField, $vField );

    if ( ! $root ['children'] or ! is_finite ( $root ['value'] ) )
      return '';

    $desc = [];
    padChartHierarchyDesc ( $root, [], $desc );

    list ( $id, $svg ) = padChartOpen ( 'pack', $root, $desc, $title, $width, $height );

    // A legend of the outermost groups when there are levels.

    $legendH = 0;

    if ( $groups and count ( $root ['children'] ) > 1 ) {
      list ( $legend, $legendH ) = padChartLegend ( array_column ( $root ['children'], 'name' ), 10, 6, $width - 20 );
      $svg    .= $legend;
      $legendH += 4;
    }

    // Packed once at radius 1 per unit to learn the scale, then again with the 3px padding
    // between the circles of a group at that scale.

    $size  = max ( 10, min ( $width, $height - $legendH ) / 2 - 4 );
    $first = padChartPackNode ( $root, 0 );
    $pad   = 3 / ( $size / max ( 1e-9, $first ['r'] ) );
    $root  = padChartPackNode ( $root, $pad );
    $k     = $size / max ( 1e-9, $root ['r'] );
    $cx    = $width / 2;
    $cy    = $legendH + ( $height - $legendH ) / 2;

    return $svg . padChartPackDraw ( $root, $cx, $cy, $k, 0, 'pc-c1', [] ) . '</svg>';

  }

  // The tree of the rows: a node per group and part, a group's value the sum of its parts,
  // the children of each node largest first.

  function padChartPackTree ( $rows, $groups, $lField, $vField ) {

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

    return padChartPackSort ( $root );

  }

  function padChartPackSort ( $node ) {

    $children = array_values ( $node ['children'] );

    foreach ( $children as $i => $child )
      $children [$i] = padChartPackSort ( $child );

    usort ( $children, fn ( $a, $b ) => [ $b ['value'], $a ['name'] ] <=> [ $a ['value'], $b ['name'] ] );

    $node ['children'] = $children;

    return $node;

  }

  // A node with its radius 'r' and each child placed at 'x', 'y' around the node's
  // centre: a part has the radius of the root of its value, a group packs its children,
  // each grown by the padding while packed, and encloses them.

  function padChartPackNode ( $node, $pad ) {

    if ( ! $node ['children'] ) {
      $node ['r'] = sqrt ( $node ['value'] );
      return $node;
    }

    $circles = [];

    foreach ( $node ['children'] as $i => $child ) {
      $node ['children'] [$i] = padChartPackNode ( $child, $pad );
      $circles [] = [ 0, 0, $node ['children'] [$i] ['r'] + $pad ];
    }

    list ( $circles, $r ) = padChartPackSiblings ( $circles );

    foreach ( $circles as $i => list ( $x, $y ) ) {
      $node ['children'] [$i] ['x'] = $x;
      $node ['children'] [$i] ['y'] = $y;
    }

    $node ['r'] = $r + $pad;

    return $node;

  }

  // Circles [ x, y, r ] packed side by side (Wang et al., the front chain d3 uses): the
  // first two touch, each next one is placed against the pair on the chain nearest the
  // middle, and when it would overlap a circle further along the chain, that circle takes
  // the place of the pair's end and it is tried again. Answers the circles moved so that
  // the smallest circle around them has its centre at 0,0, and its radius.

  function padChartPackSiblings ( $c ) {

    $n = count ( $c );

    if ( $n == 1 )
      return [ [ [ 0, 0, $c [0] [2] ] ], $c [0] [2] ];

    $c [0] [0] = -$c [1] [2];
    $c [1] [0] =  $c [0] [2];
    $c [0] [1] = $c [1] [1] = 0;

    if ( $n == 2 ) {
      $mid = ( $c [0] [0] + $c [1] [0] ) / 2 + ( $c [1] [2] - $c [0] [2] ) / 2;
      foreach ( $c as $i => $circle )
        $c [$i] [0] -= $mid;
      return [ $c, $c [0] [2] + $c [1] [2] ];
    }

    $c [2] = padChartPackPlace ( $c [1], $c [0], $c [2] );

    // The front chain as a ring of indexes: a, b and the third between them.

    $next = [ 0 => 1, 1 => 2, 2 => 0 ];
    $prev = [ 0 => 2, 1 => 0, 2 => 1 ];
    $a    = 0;
    $b    = 1;

    $hits = fn ( $p, $q ) => ( $p [2] + $q [2] - 1e-6 > 0 ) && ( ( $p [2] + $q [2] - 1e-6 ) ** 2 > ( $q [0] - $p [0] ) ** 2 + ( $q [1] - $p [1] ) ** 2 );

    $score = function ( $i ) use ( &$c, &$next ) {
      $p  = $c [$i];
      $q  = $c [$next [$i]];
      $ab = $p [2] + $q [2];
      $dx = ( $p [0] * $q [2] + $q [0] * $p [2] ) / $ab;
      $dy = ( $p [1] * $q [2] + $q [1] * $p [2] ) / $ab;
      return $dx * $dx + $dy * $dy;
    };

    for ( $i = 3; $i < $n; $i++ ) {

      $c [$i] = padChartPackPlace ( $c [$a], $c [$b], $c [$i] );

      // The nearest circle along the chain that the new one overlaps, looked for ahead of b
      // and behind a in turn, by the length of chain walked.

      $j  = $next [$b];
      $k  = $prev [$a];
      $sj = $c [$b] [2];
      $sk = $c [$a] [2];
      $redo = FALSE;

      do {
        if ( $sj <= $sk ) {
          if ( $hits ( $c [$j], $c [$i] ) ) {
            $b = $j;
            $next [$a] = $b;
            $prev [$b] = $a;
            $redo = TRUE;
            break;
          }
          $sj += $c [$j] [2];
          $j   = $next [$j];
        } else {
          if ( $hits ( $c [$k], $c [$i] ) ) {
            $a = $k;
            $next [$a] = $b;
            $prev [$b] = $a;
            $redo = TRUE;
            break;
          }
          $sk += $c [$k] [2];
          $k   = $prev [$k];
        }
      } while ( $j !== $next [$k] );

      if ( $redo ) {
        $i--;
        continue;
      }

      // Room: the new circle goes into the chain between a and b, and the pair nearest the
      // middle is the next a and b.

      $prev [$i] = $a;
      $next [$i] = $b;
      $next [$a] = $i;
      $prev [$b] = $i;
      $b = $i;

      $best = $score ( $a );
      for ( $m = $next [$b]; $m !== $b; $m = $next [$m] )
        if ( ( $s = $score ( $m ) ) < $best ) {
          $a    = $m;
          $best = $s;
        }

      $b = $next [$a];

    }

    // The circle around the chain, and every circle moved to its centre.

    $chain = [ $b ];
    for ( $m = $next [$b]; $m !== $b; $m = $next [$m] )
      $chain [] = $m;

    list ( $ex, $ey, $er ) = padChartPackEnclose ( array_map ( fn ( $m ) => $c [$m], $chain ) );

    foreach ( $c as $i => $circle ) {
      $c [$i] [0] -= $ex;
      $c [$i] [1] -= $ey;
    }

    return [ $c, $er ];

  }

  // Circle $c placed against $a and $b, on the left of the line from $b to $a.

  function padChartPackPlace ( $b, $a, $c ) {

    $dx = $b [0] - $a [0];
    $dy = $b [1] - $a [1];
    $d2 = $dx * $dx + $dy * $dy;

    if ( ! $d2 )
      return [ $a [0] + $c [2], $a [1], $c [2] ];

    $a2 = ( $a [2] + $c [2] ) ** 2;
    $b2 = ( $b [2] + $c [2] ) ** 2;

    if ( $a2 > $b2 ) {
      $x = ( $d2 + $b2 - $a2 ) / ( 2 * $d2 );
      $y = sqrt ( max ( 0, $b2 / $d2 - $x * $x ) );
      return [ $b [0] - $x * $dx - $y * $dy, $b [1] - $x * $dy + $y * $dx, $c [2] ];
    }

    $x = ( $d2 + $a2 - $b2 ) / ( 2 * $d2 );
    $y = sqrt ( max ( 0, $a2 / $d2 - $x * $x ) );

    return [ $a [0] + $x * $dx - $y * $dy, $a [1] + $x * $dy + $y * $dx, $c [2] ];

  }

  // A circle around circles, near the smallest: from the middle of their bounding box the
  // centre steps toward the circle that reaches furthest, by less each round (Badoiu and
  // Clarkson), and the radius is then what takes in all of them.

  function padChartPackEnclose ( $circles ) {

    $reach = fn ( $x, $y, $p ) => sqrt ( ( $p [0] - $x ) ** 2 + ( $p [1] - $y ) ** 2 ) + $p [2];

    $x = ( min ( array_map ( fn ( $p ) => $p [0] - $p [2], $circles ) ) + max ( array_map ( fn ( $p ) => $p [0] + $p [2], $circles ) ) ) / 2;
    $y = ( min ( array_map ( fn ( $p ) => $p [1] - $p [2], $circles ) ) + max ( array_map ( fn ( $p ) => $p [1] + $p [2], $circles ) ) ) / 2;

    for ( $round = 1; $round <= 200; $round++ ) {

      $far = $circles [0];
      $max = -INF;

      foreach ( $circles as $p )
        if ( ( $r = $reach ( $x, $y, $p ) ) > $max ) {
          $max = $r;
          $far = $p;
        }

      // The point of that circle furthest from the centre.

      $d = sqrt ( ( $far [0] - $x ) ** 2 + ( $far [1] - $y ) ** 2 );
      $px = ( $d > 0 ) ? $far [0] + ( $far [0] - $x ) / $d * $far [2] : $far [0] + $far [2];
      $py = ( $d > 0 ) ? $far [1] + ( $far [1] - $y ) / $d * $far [2] : $far [1];

      $x += ( $px - $x ) / ( $round + 1 );
      $y += ( $py - $y ) / ( $round + 1 );

    }

    $r = 0;
    foreach ( $circles as $p )
      $r = max ( $r, $reach ( $x, $y, $p ) );

    return [ $x, $y, $r ];

  }

  // The circles, outermost first: a group light in the colour of its outermost group with
  // a line of it around, a part full; a part with room for its whole name is named, and given its value under it.

  function padChartPackDraw ( $node, $x, $y, $k, $depth, $slot, $path ) {

    $svg = '';

    foreach ( $node ['children'] as $i => $child ) {

      $cx    = $x + $child ['x'] * $k;
      $cy    = $y + $child ['y'] * $k;
      $r     = max ( 0.5, $child ['r'] * $k );
      $class = $depth ? $slot : ( $child ['children'] ? padChartSlot ( $i ) : 'pc-c1' );
      $trail = array_merge ( $path, [ $child ['name'] ] );
      $tip   = '<title>' . padChartAttr ( implode ( ' › ', $trail ) . ': ' . padChartNumber ( $child ['value'] ) ) . '</title>';
      $at    = ' cx="' . padChartXY ( $cx ) . '" cy="' . padChartXY ( $cy ) . '" r="' . padChartXY ( $r ) . '"';

      if ( $child ['children'] ) {
        $svg .= "<circle class=\"$class\" fill-opacity=\"0.14\"$at>$tip</circle>"
              . padChartPackDraw ( $child, $cx, $cy, $k, $depth + 1, $class, $trail );
        continue;
      }

      $svg .= "<circle class=\"pc-mark pc-ring $class\"$at>$tip</circle>";

      if ( $r >= 14 and padChartWidth ( $name = $child ['name'] ) <= 2 * $r - 6 ) {
        $two  = ( $r >= 24 );
        $svg .= '<text class="pc-in" x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy + ( $two ? -1 : 4 ) ) . '" text-anchor="middle" pointer-events="none">' . padChartAttr ( $name ) . '</text>';
        if ( $two )
          $svg .= '<text class="pc-in" x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $cy + 12 ) . '" text-anchor="middle" pointer-events="none">' . padChartAttr ( padChartFit ( padChartNumber ( $child ['value'] ), 2 * $r - 6 ) ) . '</text>';
      }

    }

    return $svg;

  }

?>

<?php

  // Who is under whom: {chart 'orgchart', data='staff', id='id', parent='manager', label='name', sub='role'}.
  //
  // A row is a person - or any node of a tree - named by label=, else the first field that
  // is no number; id= names the field that identifies it ('id' when the rows have one, else
  // the name itself) and parent= the field that holds the id of the one above it ('parent').
  // sub= names a field shown as a second line, a job title. A row whose parent is empty or
  // not among the ids is a root; several roots stand side by side. A row whose id was seen
  // before, and rows that are their own boss through others, are left out - reported under
  // the strict check.
  //
  // The tree is drawn top down: a node over the middle of its children, a box per node,
  // elbow lines between them. The branches under the top are coloured each by its slot.
  // When the chart is too narrow for a box per node side by side, a node whose children
  // have none of their own lists them under it, one below the other.

  function padChartOrg ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $first  = padChartRow ( ( (array) $rows ) [array_key_first ( (array) $rows ) ?? 0] ?? [] );
    $iField = (string) padTagParm ( 'id', array_key_exists ( 'id', $first ) ? 'id' : '' );
    $pField = (string) padTagParm ( 'parent', 'parent' );
    $sField = (string) padTagParm ( 'sub' );
    $used   = array_filter ( [ $iField, $pField, $sField ] );
    $lField = padChartPick ( 'label', $names, $used );

    if ( $iField === '' )
      $iField = $lField;

    if ( $lField === '' ) {
      if ( $padCheckSyntax )
        padError ( "the org chart has no field for the names - label='name'" );
      return '';
    }

    $title = (string) padTagParm ( 'title', 'Organisation chart' );

    // The nodes by id, in the order of the rows.

    $nodes = [];

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $id  = padChartText ( $row, $iField );

      if ( $id === '' )
        continue;

      if ( isset ( $nodes [$id] ) ) {
        if ( $padCheckSyntax )
          padError ( "the org chart has the id '" . padMakeSafe ( $id, 40 ) . "' twice" );
        continue;
      }

      $nodes [$id] = [ 'name' => padChartText ( $row, $lField ), 'sub' => padChartText ( $row, $sField ),
                       'parent' => padChartText ( $row, $pField ), 'kids' => [] ];

    }

    if ( ! $nodes )
      return '';

    $roots = [];

    foreach ( $nodes as $id => $node )
      if ( $node ['parent'] !== '' and $node ['parent'] != $id and isset ( $nodes [$node ['parent']] ) )
        $nodes [$node ['parent']] ['kids'] [] = $id;
      else
        $roots [] = $id;

    // What a root does not reach goes round in a circle.

    $seen  = [];
    $stack = $roots;

    while ( $stack ) {
      $id = array_pop ( $stack );
      $seen [$id] = TRUE;
      foreach ( $nodes [$id] ['kids'] as $kid )
        $stack [] = $kid;
    }

    if ( count ( $seen ) < count ( $nodes ) ) {
      if ( $padCheckSyntax ) {
        $lost = array_keys ( array_diff_key ( $nodes, $seen ) );
        padError ( "the org chart goes round in a circle through '" . padMakeSafe ( (string) $lost [0], 40 ) . "'" );
      }
      $nodes = array_intersect_key ( $nodes, $seen );
    }

    if ( ! $roots )
      return '';

    $desc = [];
    foreach ( $nodes as $id => $node )
      $desc [] = $node ['name'] . ( $node ['sub'] !== '' ? ', ' . $node ['sub'] : '' )
               . ( isset ( $nodes [$node ['parent']] ) ? ' - under ' . $nodes [$node ['parent']] ['name'] : '' );

    list ( $id, $svg ) = padChartOpen ( 'orgchart', $nodes, $desc, $title, $width, $height );

    // The columns: a node without children takes one, a node is over its children. Too
    // narrow for that, the children without children of their own are listed under their
    // node in its one column.

    $leaves = fn ( $id ) => $nodes [$id] ['kids'] and ! array_filter ( $nodes [$id] ['kids'], fn ( $kid ) => $nodes [$kid] ['kids'] );

    $list    = FALSE;
    $columns = padChartOrgColumns ( $nodes, $roots, $list, $leaves );

    if ( ( $width - 20 ) / max ( 1, $columns ) < 96 ) {
      $list    = TRUE;
      $columns = padChartOrgColumns ( $nodes, $roots, $list, $leaves );
    }

    $place = [];
    $at    = 0;

    foreach ( $roots as $root )
      padChartOrgPlace ( $nodes, $root, 0, $at, $list, $leaves, $place );

    // The sizes: the boxes as wide as a column allows, the levels spread over the height.

    $two   = ( $sField !== '' );
    $colW  = ( $width - 20 ) / max ( 1, $columns );
    $boxW  = max ( 30, min ( 170, $colW - 12 ) );
    $boxH  = $two ? 38 : 26;
    $depth = max ( array_column ( $place, 1 ) );
    $under = max ( array_column ( $place, 2 ) );

    $listStep  = $boxH + 8;
    $spare     = $height - 20 - $boxH * ( $depth + 1 ) - $under * $listStep;
    $levelStep = $boxH + max ( 16, min ( 48, $depth ? $spare / $depth : 0 ) );

    $box = function ( $id ) use ( $place, $colW, $boxW, $levelStep, $listStep ) {
      list ( $col, $level, $row ) = $place [$id];
      $x = 10 + ( $col + 0.5 ) * $colW - $boxW / 2;
      $y = 10 + $level * $levelStep + $row * $listStep;
      return $row ? [ $x + 14, $y, $boxW - 14 ] : [ $x, $y, $boxW ];
    };

    // The slots: one per branch under a single root, else one per root.

    $slot = [];
    $tops = ( count ( $roots ) == 1 ) ? $nodes [$roots [0]] ['kids'] : $roots;

    foreach ( $tops as $i => $top ) {
      $stack = [ $top ];
      while ( $stack ) {
        $one = array_pop ( $stack );
        $slot [$one] = $i;
        foreach ( $nodes [$one] ['kids'] as $kid )
          $stack [] = $kid;
      }
    }

    // The lines: down from a node, across, and down into each child - or, for a list, down
    // its left side and across into each of the listed.

    foreach ( $nodes as $id => $node ) {

      if ( ! $node ['kids'] )
        continue;

      list ( $px, $py, $pw ) = $box ( $id );
      $bottom = $py + $boxH;

      if ( $place [$node ['kids'] [0]] [2] ) {
        $lx = $px + 7;
        $d  = '';
        foreach ( $node ['kids'] as $kid ) {
          list ( $kx, $ky ) = $box ( $kid );
          $d .= 'M' . padChartXY ( $lx ) . ',' . padChartXY ( $bottom ) . 'V' . padChartXY ( $ky + $boxH / 2 ) . 'H' . padChartXY ( $kx );
        }
        $svg .= "<path class=\"pc-edge\" stroke-width=\"1.5\" d=\"$d\"/>";
        continue;
      }

      $mid = $bottom + ( $levelStep - $boxH ) / 2;
      $d   = '';

      foreach ( $node ['kids'] as $kid ) {
        list ( $kx, $ky, $kw ) = $box ( $kid );
        $d .= 'M' . padChartXY ( $px + $pw / 2 ) . ',' . padChartXY ( $bottom ) . 'V' . padChartXY ( $mid )
            . 'H' . padChartXY ( $kx + $kw / 2 ) . 'V' . padChartXY ( $ky );
      }

      $svg .= "<path class=\"pc-edge\" stroke-width=\"1.5\" d=\"$d\"/>";

    }

    // The boxes: a light fill in the colour of the branch with a line of it around, the
    // name and under it the second line, both cut to the box.

    foreach ( $nodes as $id => $node ) {

      list ( $x, $y, $w ) = $box ( $id );

      $i    = $slot [$id] ?? -1;
      $fill = ( $i < 0 ) ? 'pc-co' : padChartSlot ( $i );
      $line = ( $i < 0 or $i >= 8 ) ? 'pc-edge' : 'pc-l' . ( $i + 1 );
      $at   = ' x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y ) . '" width="' . padChartXY ( $w ) . "\" height=\"$boxH\" rx=\"6\"";
      $boss = isset ( $nodes [$node ['parent']] ) ? ' - under ' . $nodes [$node ['parent']] ['name'] : '';

      $svg .= "<rect class=\"pc-mark $fill\" fill-opacity=\"0.16\"$at><title>"
            . padChartAttr ( $node ['name'] . ( $node ['sub'] !== '' ? ', ' . $node ['sub'] : '' ) . $boss ) . '</title></rect>'
            . "<rect class=\"$line\" style=\"stroke-width:1.5\" pointer-events=\"none\"$at/>";

      $tx = $x + $w / 2;

      if ( $two ) {
        $svg .= '<text x="' . padChartXY ( $tx ) . '" y="' . padChartXY ( $y + 16 ) . '" text-anchor="middle" font-weight="600" pointer-events="none">' . padChartAttr ( padChartFit ( $node ['name'], $w - 10 ) ) . '</text>'
              . '<text x="' . padChartXY ( $tx ) . '" y="' . padChartXY ( $y + 30 ) . '" text-anchor="middle" opacity="0.8" pointer-events="none">' . padChartAttr ( padChartFit ( $node ['sub'], $w - 10 ) ) . '</text>';
      } else
        $svg .= '<text x="' . padChartXY ( $tx ) . '" y="' . padChartXY ( $y + 17 ) . '" text-anchor="middle" font-weight="600" pointer-events="none">' . padChartAttr ( padChartFit ( $node ['name'], $w - 10 ) ) . '</text>';

    }

    return "$svg</svg>";

  }

  // The number of columns the trees take: one per node without children - or per node
  // that lists its children - and the sum of its children's for any other.

  function padChartOrgColumns ( $nodes, $ids, $list, $leaves ) {

    $count = 0;

    foreach ( $ids as $id )
      if ( ! $nodes [$id] ['kids'] or ( $list and $leaves ( $id ) ) )
        $count++;
      else
        $count += padChartOrgColumns ( $nodes, $nodes [$id] ['kids'], $list, $leaves );

    return $count;

  }

  // The place of each node: [ column, level, row in a list ]. A node without children
  // takes the next column, a node with them stands over the middle of its first and last
  // child; a listed child keeps its node's column and level, one row further down each.

  function padChartOrgPlace ( $nodes, $id, $level, &$at, $list, $leaves, &$place ) {

    $kids = $nodes [$id] ['kids'];

    if ( ! $kids ) {
      $place [$id] = [ $at++, $level, 0 ];
      return;
    }

    if ( $list and $leaves ( $id ) ) {
      $place [$id] = [ $at, $level, 0 ];
      foreach ( $kids as $row => $kid )
        $place [$kid] = [ $at, $level, $row + 1 ];
      $at++;
      return;
    }

    foreach ( $kids as $kid )
      padChartOrgPlace ( $nodes, $kid, $level + 1, $at, $list, $leaves, $place );

    $place [$id] = [ ( $place [$kids [0]] [0] + $place [end ( $kids )] [0] ) / 2, $level, 0 ];

  }

?>

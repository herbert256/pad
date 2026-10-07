<?php

  // Patterns in dense data: a grid of two categories, each cell coloured by its value.
  //
  //   {chart 'heatmap', data='visits', x='hour', y='day', value='count'}
  //
  // y= names the category of the rows and x= that of the columns - else the first two
  // fields that are no number - and value= the number, else the first numeric field (row=
  // and group= would be the handling options of every tag). The categories keep the order
  // they first appear in; rows with the same row and column add up. The colour is one hue
  // from light to dark in seven steps, low to high, with a scale under the grid; a cell
  // with room for it shows its value, and every cell has its tooltip.

  function padChartHeatmap ( $rows, $width, $height ) {

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $rField = padChartPick ( 'y',     $names,   $used );
    $cField = padChartPick ( 'x',     $names,   $used );
    $vField = padChartPick ( 'value',  $numbers, $used );

    $title = (string) padTagParm ( 'title', ucfirst ( $vField !== '' ? $vField : 'value' ) . " by $rField and $cField" );

    $cells = $rowNames = $colNames = [];

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $v   = $row [$vField] ?? NULL;

      if ( ! padChartFinite ( $v ) )
        continue;

      $r = padChartText ( $row, $rField );
      $c = padChartText ( $row, $cField );

      $rowNames [$r] = TRUE;
      $colNames [$c] = TRUE;
      $cells [$r] [$c] = ( $cells [$r] [$c] ?? 0 ) + $v;

    }

    if ( ! $cells )
      return '';

    $rowNames = array_map ( 'strval', array_keys ( $rowNames ) );
    $colNames = array_map ( 'strval', array_keys ( $colNames ) );
    $desc     = [];
    $all      = [];

    foreach ( $cells as $r => $line )
      foreach ( $line as $c => $v ) {
        $desc [] = "$r · $c: " . padChartNumber ( $v );
        $all  [] = $v;
      }

    list ( $id, $svg ) = padChartOpen ( 'heatmap', $cells, $desc, $title, $width, $height );

    $min  = min ( $all );
    $max  = max ( $all );
    $span = $max - $min;
    $step = fn ( $v ) => ( is_finite ( $span ) and $span > 0 ) ? (int) round ( ( $v - $min ) / $span * 6 ) : 6;

    $labelWidth = 0;
    foreach ( $rowNames as $name )
      $labelWidth = max ( $labelWidth, padChartWidth ( $name ) + 8 );

    $colWidth = 0;
    foreach ( $colNames as $name )
      $colWidth = max ( $colWidth, padChartWidth ( $name ) + 6 );

    $left   = (int) max ( 24, min ( $width * 0.3, $labelWidth + 4 ) );
    $top    = 10;
    $right  = 10;
    $bottom = 46;
    $cellW  = max ( 1, $width  - $left - $right ) / count ( $colNames );
    $cellH  = max ( 1, $height - $top  - $bottom ) / count ( $rowNames );
    $gridB  = $top + $cellH * count ( $rowNames );

    $everyRow = max ( 1, (int) ceil ( 12 / $cellH ) );
    $everyCol = max ( 1, (int) ceil ( $colWidth / $cellW ) );

    foreach ( $rowNames as $i => $name )
      if ( $i % $everyRow == 0 )
        $svg .= '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $top + ( $i + 0.5 ) * $cellH + 4 ) . '" text-anchor="end">' . padChartAttr ( padChartFit ( $name, $left - 10 ) ) . '</text>';

    foreach ( $colNames as $j => $name )
      if ( $j % $everyCol == 0 )
        $svg .= '<text x="' . padChartXY ( $left + ( $j + 0.5 ) * $cellW ) . '" y="' . padChartXY ( $gridB + 15 ) . '" text-anchor="middle">' . padChartAttr ( $name ) . '</text>';

    foreach ( $rowNames as $i => $r )
      foreach ( $colNames as $j => $c ) {

        if ( ! isset ( $cells [$r] [$c] ) )
          continue;

        $v = $cells [$r] [$c];
        $x = $left + $j * $cellW;
        $y = $top  + $i * $cellH;

        $svg .= '<rect class="pc-mark pc-gap pc-h' . $step ( $v ) . '" x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y ) . '" width="' . padChartXY ( $cellW ) . '" height="' . padChartXY ( $cellH ) . '">'
              . '<title>' . padChartAttr ( "$r · $c: " . padChartNumber ( $v ) ) . '</title></rect>';

        $text = padChartNumber ( $v );

        if ( $cellH >= 16 and padChartWidth ( $text ) + 8 <= $cellW )
          $svg .= '<text' . ( $step ( $v ) >= 4 ? ' class="pc-in"' : '' ) . ' x="' . padChartXY ( $x + $cellW / 2 ) . '" y="' . padChartXY ( $y + $cellH / 2 + 4 ) . '" text-anchor="middle" pointer-events="none">' . $text . '</text>';

      }

    // The scale: the lowest value, the seven steps, the highest.

    $low  = padChartNumber ( $min );
    $x    = $left + padChartWidth ( $low ) + 6;
    $y    = $height - 16;
    $svg .= '<text x="' . $left . '" y="' . padChartXY ( $y + 9 ) . '">' . $low . '</text>';

    for ( $k = 0; $k <= 6; $k++ )
      $svg .= '<rect class="pc-h' . $k . '" x="' . padChartXY ( $x + $k * 16 ) . '" y="' . padChartXY ( $y ) . '" width="16" height="10"/>';

    $svg .= '<text x="' . padChartXY ( $x + 7 * 16 + 6 ) . '" y="' . padChartXY ( $y + 9 ) . '">' . padChartNumber ( $max ) . '</text>';

    return "$svg</svg>";

  }

?>

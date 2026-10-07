<?php

  // How values spread - the histogram and the box plot.
  //
  //   {chart 'histogram', data='people', value='height', bins=12}
  //   {chart 'boxplot', data='salaries', label='department', value='salary'}
  //
  // value= names the numbers - else the first numeric field. A histogram counts them in
  // about bins= (10) bins of one round width, edge to edge - a whole width for whole
  // numbers; a value on an edge goes in the bin above it, the highest value in the last bin. A box plot draws a box per group of
  // label= - all values in one box without it - from the first to the third quartile with
  // the median across it, whiskers to the furthest values within 1.5 times the box's
  // height, and the values past them as dots.

  function padChartValues ( $rows, $field ) {

    $values = [];

    foreach ( (array) $rows as $row ) {
      $row = padChartRow ( $row );
      $v   = $row [$field] ?? NULL;
      if ( padChartFinite ( $v ) )
        $values [] = $v + 0;
    }

    return $values;

  }

  // The frame both kinds share: the value axis on the left, ticks and grid; answers the
  // svg so far and the functions that place a value and a band.

  function padChartValueAxis ( $svg, $ticks, $width, $height, $top, $bottom ) {

    $low   = $ticks [0];
    $high  = end ( $ticks );
    $tickW = 0;

    foreach ( $ticks as $tick )
      $tickW = max ( $tickW, padChartWidth ( padChartNumber ( $tick ) ) );

    $left  = max ( 24, (int) ceil ( $tickW ) + 10 );
    $plotH = $height - $top - $bottom;
    $y     = fn ( $v ) => $top + ( $high - $v ) / ( $high - $low ) * $plotH;

    foreach ( $ticks as $tick )
      $svg .= '<line class="pc-grid" x1="' . $left . '" x2="' . ( $width - 10 ) . '" y1="' . padChartXY ( $y ( $tick ) ) . '" y2="' . padChartXY ( $y ( $tick ) ) . '"/>'
            . '<text x="' . ( $left - 6 ) . '" y="' . padChartXY ( $y ( $tick ) + 4 ) . '" text-anchor="end">' . padChartNumber ( $tick ) . '</text>';

    return [ $svg, $left, $y ];

  }

  function padChartHistogram ( $rows, $width, $height ) {

    list ( $numbers ) = padChartGuess ( $rows );

    $used   = [];
    $field  = padChartPick ( 'value', $numbers, $used );
    $values = padChartValues ( $rows, $field );

    if ( ! $values )
      return '';

    $edges = padChartTicks ( min ( $values ), max ( $values ), max ( 1, min ( 100, (int) padTagParm ( 'bins', 10 ) ) ) );

    if ( count ( $edges ) < 2 )
      $edges [] = $edges [0] + 1;

    // Whole numbers are counted in bins of whole numbers: a bin from 2.5 to 5 of days holds
    // 3, 4 and 5, and its neighbour two - bins of unequal content under equal widths.

    $step = $edges [1] - $edges [0];

    if ( $step != floor ( $step ) and ! array_filter ( $values, fn ( $v ) => $v != floor ( $v ) ) ) {
      $step  = ceil ( $step );
      $edges = [];
      for ( $edge = floor ( min ( $values ) / $step ) * $step; ! $edges or end ( $edges ) <= max ( $values ); $edge += $step )
        $edges [] = $edge;
    }

    $bins   = count ( $edges ) - 1;
    $counts = array_fill ( 0, $bins, 0 );

    foreach ( $values as $v )
      $counts [ max ( 0, min ( $bins - 1, (int) floor ( ( $v - $edges [0] ) / $step ) ) ) ]++;

    $range = fn ( $i ) => padChartNumber ( $edges [$i] ) . ' – ' . padChartNumber ( $edges [$i + 1] );
    $desc  = [];

    foreach ( $counts as $i => $count )
      $desc [] = $range ( $i ) . ": $count";

    $title = (string) padTagParm ( 'title', ucfirst ( $field !== '' ? $field : 'value' ) . ', ' . count ( $values ) . ' values' );

    list ( $id, $svg ) = padChartOpen ( 'histogram', [ $edges, $counts ], $desc, $title, $width, $height );

    $ticks = padChartTicks ( 0, max ( $counts ), $height < 200 ? 3 : 5 );
    $ticks = array_values ( array_filter ( $ticks, fn ( $t ) => abs ( $t - round ( $t ) ) < 1e-9 ) );

    list ( $svg, $left, $y ) = padChartValueAxis ( $svg, $ticks, $width, $height, 10, 24 );

    $plotW = $width - $left - 10;
    $binW  = $plotW / $bins;
    $edgeW = 0;

    foreach ( $edges as $edge )
      $edgeW = max ( $edgeW, padChartWidth ( padChartNumber ( $edge ) ) + 8 );

    $every = max ( 1, (int) ceil ( $edgeW / $binW ) );

    foreach ( $counts as $i => $count ) {
      $x = $left + $i * $binW;
      if ( $count )
        $svg .= '<rect class="pc-mark pc-gap pc-c1" x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $y ( $count ) ) . '" width="' . padChartXY ( $binW ) . '" height="' . padChartXY ( $y ( 0 ) - $y ( $count ) ) . '">'
              . '<title>' . padChartAttr ( $desc [$i] ) . '</title></rect>';
    }

    foreach ( $edges as $i => $edge )
      if ( $i % $every == 0 )
        $svg .= '<text x="' . padChartXY ( $left + $i * $binW ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartNumber ( $edge ) . '</text>';

    $svg .= '<line class="pc-axis" x1="' . $left . '" x2="' . ( $width - 10 ) . '" y1="' . padChartXY ( $y ( 0 ) ) . '" y2="' . padChartXY ( $y ( 0 ) ) . '"/>';

    return "$svg</svg>";

  }

  // A quantile of sorted values, interpolated between the two around it.

  function padChartQuantile ( $sorted, $q ) {

    $at   = ( count ( $sorted ) - 1 ) * $q;
    $low  = (int) floor ( $at );
    $high = (int) ceil ( $at );

    return $sorted [$low] + ( $sorted [$high] - $sorted [$low] ) * ( $at - $low );

  }

  function padChartBoxplot ( $rows, $width, $height ) {

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $field  = padChartPick ( 'value', $numbers, $used );
    $label  = (string) padTagParm ( 'label' );
    $groups = [];

    foreach ( (array) $rows as $row ) {
      $row = padChartRow ( $row );
      $v   = $row [$field] ?? NULL;
      if ( padChartFinite ( $v ) )
        $groups [ $label !== '' ? padChartText ( $row, $label ) : ucfirst ( $field ) ] [] = $v + 0;
    }

    if ( ! $groups )
      return '';

    $boxes = [];
    $desc  = [];

    foreach ( $groups as $name => $values ) {

      sort ( $values );

      $q1  = padChartQuantile ( $values, 0.25 );
      $med = padChartQuantile ( $values, 0.5 );
      $q3  = padChartQuantile ( $values, 0.75 );
      $iqr = $q3 - $q1;
      $in  = array_filter ( $values, fn ( $v ) => $v >= $q1 - 1.5 * $iqr and $v <= $q3 + 1.5 * $iqr );
      $out = array_filter ( $values, fn ( $v ) => $v < $q1 - 1.5 * $iqr or $v > $q3 + 1.5 * $iqr );

      $boxes [$name] = [ min ( $in ), $q1, $med, $q3, max ( $in ), array_values ( $out ), count ( $values ) ];

      $desc [] = "$name: median " . padChartNumber ( $med ) . ', quartiles ' . padChartNumber ( $q1 ) . ' – ' . padChartNumber ( $q3 )
               . ', range ' . padChartNumber ( $values [0] ) . ' – ' . padChartNumber ( end ( $values ) ) . ', ' . count ( $values ) . ' values';

    }

    $title = (string) padTagParm ( 'title', ucfirst ( $field !== '' ? $field : 'value' ) . ( $label !== '' ? " by $label" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'boxplot', $boxes, $desc, $title, $width, $height );

    $all = [];
    foreach ( $groups as $values )
      $all = array_merge ( $all, $values );

    list ( $svg, $left, $y ) = padChartValueAxis ( $svg, padChartTicks ( min ( $all ), max ( $all ), $height < 200 ? 3 : 5 ), $width, $height, 10, 24 );

    $n    = count ( $boxes );
    $band = ( $width - $left - 10 ) / $n;
    $boxW = max ( 6, min ( 56, $band * 0.5 ) );
    $i    = 0;

    foreach ( $boxes as $name => list ( $lo, $q1, $med, $q3, $hi, $out, $count ) ) {

      $c    = $left + ( $i + 0.5 ) * $band;
      $x0   = $c - $boxW / 2;
      $slot = padChartSlot ( $n > 1 ? $i : 0 );
      $tip  = '<title>' . padChartAttr ( $desc [$i] ) . '</title>';

      $svg .= '<line class="pc-whisker" x1="' . padChartXY ( $c ) . '" x2="' . padChartXY ( $c ) . '" y1="' . padChartXY ( $y ( $hi ) ) . '" y2="' . padChartXY ( $y ( $q3 ) ) . '"/>'
            . '<line class="pc-whisker" x1="' . padChartXY ( $c ) . '" x2="' . padChartXY ( $c ) . '" y1="' . padChartXY ( $y ( $q1 ) ) . '" y2="' . padChartXY ( $y ( $lo ) ) . '"/>'
            . '<line class="pc-whisker" x1="' . padChartXY ( $c - $boxW / 4 ) . '" x2="' . padChartXY ( $c + $boxW / 4 ) . '" y1="' . padChartXY ( $y ( $hi ) ) . '" y2="' . padChartXY ( $y ( $hi ) ) . '"/>'
            . '<line class="pc-whisker" x1="' . padChartXY ( $c - $boxW / 4 ) . '" x2="' . padChartXY ( $c + $boxW / 4 ) . '" y1="' . padChartXY ( $y ( $lo ) ) . '" y2="' . padChartXY ( $y ( $lo ) ) . '"/>'
            . '<rect class="pc-mark pc-box ' . $slot . '" x="' . padChartXY ( $x0 ) . '" y="' . padChartXY ( $y ( $q3 ) ) . '" width="' . padChartXY ( $boxW ) . '" height="' . padChartXY ( max ( 1, $y ( $q1 ) - $y ( $q3 ) ) ) . "\" rx=\"3\">$tip</rect>"
            . '<line class="pc-median" x1="' . padChartXY ( $x0 ) . '" x2="' . padChartXY ( $x0 + $boxW ) . '" y1="' . padChartXY ( $y ( $med ) ) . '" y2="' . padChartXY ( $y ( $med ) ) . '"/>';

      foreach ( $out as $v )
        $svg .= '<circle class="' . $slot . ' pc-ring" cx="' . padChartXY ( $c ) . '" cy="' . padChartXY ( $y ( $v ) ) . '" r="4.5"><title>' . padChartAttr ( "$name: " . padChartNumber ( $v ) ) . '</title></circle>';

      $svg .= '<text x="' . padChartXY ( $c ) . '" y="' . ( $height - 8 ) . '" text-anchor="middle">' . padChartAttr ( padChartFit ( $name, $band - 4 ) ) . '</text>';

      $i++;

    }

    return "$svg</svg>";

  }

?>

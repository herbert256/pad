<?php

  // Measures against their targets - a bullet graph per row, Stephen Few's compact gauge.
  //
  //   {chart 'bullet', data='kpis', label='kpi', value='actual', target='goal', bands='50, 75'}
  //   {chart 'bullet', data='kpis', value='actual', target='goal', bands='poor, fair', to='max'}
  //
  // A row is a horizontal track with a thin bar of value= - else the first numeric field -
  // in the series colour, and target= - a field of the row, or one number for every row -
  // as a short upright line across it. label= names the row on the left. bands= lists up
  // to four thresholds that split the track into grey steps, darkest at the bottom: a
  // number is a percentage of the row's scale, a name a field of the row that holds the
  // threshold itself. to= is the end of the scale - one number for every row, or a field
  // of the row; else each row gets round numbers past the highest of its value, target and
  // bands, so rows of different measures each fill the width. Each scale has a few ticks
  // under its track.

  function padChartBullet ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $used   = [];
    $field  = padChartPick ( 'value', $numbers, $used );
    $label  = padChartPick ( 'label', $names, $used );
    $target = (string) padTagParm ( 'target' );
    $to     = (string) padTagParm ( 'to' );
    $bands  = padChartFields ( padTagParm ( 'bands' ) );

    if ( count ( $bands ) > 4 ) {
      if ( $padCheckSyntax )
        padError ( 'a bullet chart takes four bands at most, it has ' . count ( $bands ) );
      $bands = array_slice ( $bands, 0, 4 );
    }

    if ( $to !== '' and is_numeric ( $to ) and ( ! padChartFinite ( $to ) or $to <= 0 ) ) {
      if ( $padCheckSyntax )
        padError ( 'the scale of a bullet chart ends at a number above 0' );
      return '';
    }

    // Per row: its name, value, target, scale and the thresholds of its bands.

    $bullets = [];
    $desc    = [];
    $index   = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $v   = $row [$field] ?? NULL;

      if ( ! padChartFinite ( $v ) or $v < 0 )
        continue;

      $v    = $v + 0;
      $goal = is_numeric ( $target ) ? $target : ( $target !== '' ? ( $row [$target] ?? NULL ) : NULL );
      $goal = padChartFinite ( $goal ) ? $goal + 0 : NULL;
      $end  = is_numeric ( $to ) ? $to : ( $to !== '' ? ( $row [$to] ?? NULL ) : NULL );
      $end  = ( padChartFinite ( $end ) and $end > 0 ) ? $end + 0 : NULL;
      $cuts = [];

      foreach ( $bands as $band )
        if ( ! is_numeric ( $band ) and padChartFinite ( $row [$band] ?? NULL ) )
          $cuts [] = [ FALSE, $row [$band] + 0 ];
        elseif ( is_numeric ( $band ) )
          $cuts [] = [ TRUE, $band / 100 ];

      if ( $end === NULL ) {
        $high = max ( $v, $goal ?? 0, ...array_map ( fn ( $c ) => $c [0] ? 0 : $c [1], $cuts ?: [ [ TRUE, 0 ] ] ) );
        $round = padChartTicks ( 0, $high > 0 ? $high : 1, 4 );
        $end   = end ( $round );
      }

      $cuts = array_map ( fn ( $c ) => $c [0] ? $c [1] * $end : $c [1], $cuts );
      $cuts = array_values ( array_filter ( $cuts, fn ( $c ) => $c > 0 and $c < $end ) );
      sort ( $cuts );

      $name = padChartText ( $row, $label );
      $name = ( $name !== '' ) ? $name : "Row $index";

      $bullets [] = [ $name, $v, $goal, $end, $cuts ];
      $desc    [] = "$name: " . padChartNumber ( $v ) . ( $goal !== NULL ? ', target ' . padChartNumber ( $goal ) : '' )
                  . ', scale 0 – ' . padChartNumber ( $end );

    }

    if ( ! $bullets )
      return '';

    $title = (string) padTagParm ( 'title', ucfirst ( $field !== '' ? $field : 'value' ) . ( $target !== '' && ! is_numeric ( $target ) ? " against $target" : '' ) );

    list ( $id, $svg ) = padChartOpen ( 'bullet', $bullets, $desc, $title, $width, $height );

    // The names on the left, at most a third of the width; then a track per row, its
    // ticks under it.

    $labelW = 0;
    foreach ( $bullets as $bullet )
      $labelW = max ( $labelW, padChartWidth ( $bullet [0] ) );

    $n      = count ( $bullets );
    $left   = 12 + min ( $labelW, $width / 3 ) + 12;
    $right  = 16;
    $plotW  = max ( 10, $width - $left - $right );
    $rowH   = ( $height - 12 ) / $n;
    $trackH = max ( 6, min ( 28, $rowH - 26 ) );

    foreach ( $bullets as $i => list ( $name, $v, $goal, $end, $cuts ) ) {

      $top   = 6 + $i * $rowH + ( $rowH - $trackH - 16 ) / 2;
      $x     = fn ( $at ) => $left + min ( 1, max ( 0, $at / $end ) ) * $plotW;
      $edges = array_merge ( [ 0 ], $cuts, [ $end ] );
      $steps = count ( $edges ) - 1;

      // The bands from dark to light: the grey of 'Other' at falling opacity.

      for ( $b = 0; $b < $steps; $b++ )
        $svg .= '<rect class="pc-co" fill-opacity="' . padChartXY ( $steps == 1 ? 0.2 : 0.5 - 0.35 * $b / ( $steps - 1 ) ) . '" x="' . padChartXY ( $x ( $edges [$b] ) ) . '" y="' . padChartXY ( $top ) . '"'
              . ' width="' . padChartXY ( $x ( $edges [$b + 1] ) - $x ( $edges [$b] ) ) . '" height="' . padChartXY ( $trackH ) . '">'
              . '<title>' . padChartAttr ( "$name: " . padChartNumber ( $edges [$b] ) . ' – ' . padChartNumber ( $edges [$b + 1] ) ) . '</title></rect>';

      $barH = max ( 2, $trackH / 3 );
      $svg .= '<rect class="pc-mark pc-c1" x="' . padChartXY ( $left ) . '" y="' . padChartXY ( $top + ( $trackH - $barH ) / 2 ) . '" width="' . padChartXY ( max ( 0.5, $x ( $v ) - $left ) ) . '" height="' . padChartXY ( $barH ) . '">'
            . '<title>' . padChartAttr ( $desc [$i] ) . '</title></rect>';

      if ( $goal !== NULL )
        $svg .= '<line class="pc-axis" style="stroke-width:2.5" x1="' . padChartXY ( $x ( $goal ) ) . '" x2="' . padChartXY ( $x ( $goal ) ) . '"'
              . ' y1="' . padChartXY ( $top + $trackH * 0.15 ) . '" y2="' . padChartXY ( $top + $trackH * 0.85 ) . '">'
              . '<title>' . padChartAttr ( "$name: target " . padChartNumber ( $goal ) ) . '</title></line>';

      $svg .= '<text x="' . padChartXY ( $left - 12 ) . '" y="' . padChartXY ( $top + $trackH / 2 + 4 ) . '" text-anchor="end">' . padChartAttr ( padChartFit ( $name, $left - 24 ) ) . '</text>';

      // The ticks: round numbers of the row's own scale, as many as fit.

      $ticks = padChartTicks ( 0, $end, max ( 2, min ( 6, (int) ( $plotW / 70 ) ) ) );

      foreach ( $ticks as $tick )
        if ( $tick <= $end + 1e-9 )
          $svg .= '<line class="pc-axis" x1="' . padChartXY ( $x ( $tick ) ) . '" x2="' . padChartXY ( $x ( $tick ) ) . '" y1="' . padChartXY ( $top + $trackH ) . '" y2="' . padChartXY ( $top + $trackH + 3 ) . '"/>'
                . '<text x="' . padChartXY ( $x ( $tick ) ) . '" y="' . padChartXY ( $top + $trackH + 14 ) . '" text-anchor="middle" style="font-size:9.5px">' . padChartNumber ( $tick ) . '</text>';

    }

    return "$svg</svg>";

  }

?>

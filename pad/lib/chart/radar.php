<?php

  // Many measures at once: a spoke per measure, a polygon per row.
  //
  //   {chart 'radar', data='phones', label='model', value='battery, camera, screen, speed, price'}
  //
  // value= lists the measures, three or more - else every numeric field of the first row;
  // label= names the rows, each a polygon in its own colour, eight at most. The scale runs
  // from 0 to to= - else round numbers past the highest value - in rings, each named on
  // the first spoke but the outer one.

  function padChartRadar ( $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $fields = padChartFields ( padTagParm ( 'value' ) ) ?: $numbers;
    $used   = $fields;
    $label  = padChartPick ( 'label', $names, $used );

    if ( count ( $fields ) < 3 ) {
      if ( $padCheckSyntax )
        padError ( 'a radar chart needs three measures or more in value=, it has ' . count ( $fields ) );
      return '';
    }

    $series = [];
    $index  = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $values = [];

      foreach ( $fields as $field ) {
        $v = $row [$field] ?? NULL;
        if ( ! padChartFinite ( $v ) or $v < 0 )
          continue 2;
        $values [] = $v + 0;
      }

      $name = padChartText ( $row, $label );
      $series [ $name !== '' ? $name : "Row $index" ] = $values;

    }

    if ( ! $series )
      return '';

    if ( count ( $series ) > 8 ) {
      if ( $padCheckSyntax )
        padError ( 'the radar chart has ' . count ( $series ) . ' rows - eight at most, each its own colour' );
      $series = array_slice ( $series, 0, 8, TRUE );
    }

    $top   = padTagParm ( 'to' );
    $high  = max ( array_map ( 'max', $series ) );
    $ticks = padChartTicks ( 0, padChartFinite ( $top ) && $top > 0 ? $top + 0 : max ( $high, 1e-9 ), 4 );
    $scale = end ( $ticks );
    $n     = count ( $fields );
    $desc  = [];

    foreach ( $series as $name => $values ) {
      $parts = [];
      foreach ( $fields as $i => $field )
        $parts [] = "$field " . padChartNumber ( $values [$i] );
      $desc [] = "$name: " . implode ( ', ', $parts );
    }

    $title = (string) padTagParm ( 'title', implode ( ', ', array_map ( 'ucfirst', $fields ) ) );

    list ( $id, $svg ) = padChartOpen ( 'radar', [ $fields, $series, $scale ], $desc, $title, $width, $height );

    $legendH = 0;

    if ( count ( $series ) > 1 ) {
      list ( $legend, $legendH ) = padChartLegend ( array_keys ( $series ), 10, 6, $width - 20 );
      $svg .= $legend;
    }

    $labelW = 0;
    foreach ( $fields as $field )
      $labelW = max ( $labelW, padChartWidth ( $field ) );

    $r  = max ( 20, min ( ( $width - 2 * min ( $labelW, $width * 0.25 ) ) / 2 - 12, ( $height - $legendH - 40 ) / 2 ) );
    $cx = $width / 2;
    $cy = $legendH + 20 + $r;
    $pt = fn ( $i, $v ) => [ $cx + $v / $scale * $r * sin ( 2 * M_PI * $i / $n ), $cy - $v / $scale * $r * cos ( 2 * M_PI * $i / $n ) ];

    // The rings at the ticks, the spokes, and the measures named past their spokes.

    foreach ( $ticks as $tick ) {
      if ( $tick <= 0 )
        continue;
      $d = '';
      for ( $i = 0; $i < $n; $i++ ) {
        list ( $x, $y ) = $pt ( $i, $tick );
        $d .= ( $i ? 'L' : 'M' ) . padChartXY ( $x ) . ',' . padChartXY ( $y );
      }
      $svg .= "<path class=\"pc-grid\" fill=\"none\" d=\"{$d}Z\"/>";
      if ( $tick < $scale )
        $svg .= '<text class="pc-tick" x="' . padChartXY ( $cx + 4 ) . '" y="' . padChartXY ( $cy - $tick / $scale * $r - 3 ) . '">' . padChartNumber ( $tick ) . '</text>';
    }

    foreach ( $fields as $i => $field ) {
      list ( $x, $y ) = $pt ( $i, $scale );
      $svg .= '<line class="pc-grid" x1="' . padChartXY ( $cx ) . '" y1="' . padChartXY ( $cy ) . '" x2="' . padChartXY ( $x ) . '" y2="' . padChartXY ( $y ) . '"/>';
      $s      = sin ( 2 * M_PI * $i / $n );
      $c      = cos ( 2 * M_PI * $i / $n );
      $anchor = ( $s > 0.2 ) ? 'start' : ( $s < -0.2 ? 'end' : 'middle' );
      $room   = ( $anchor == 'middle' ) ? $width : ( $anchor == 'start' ? $width - $x - 10 : $x - 10 );
      $svg   .= '<text x="' . padChartXY ( $x + 8 * $s ) . '" y="' . padChartXY ( $y - 8 * $c + 4 + ( $c < -0.5 ? 6 : 0 ) ) . "\" text-anchor=\"$anchor\">"
              . padChartAttr ( padChartFit ( ucfirst ( $field ), $room ) ) . '</text>';
    }

    // A polygon per row: a light fill, its outline, a point per measure with its tooltip.

    $j = 0;

    foreach ( $series as $name => $values ) {

      $d = $dots = '';

      foreach ( $values as $i => $v ) {
        list ( $x, $y ) = $pt ( $i, min ( $v, $scale ) );
        $d    .= ( $i ? 'L' : 'M' ) . padChartXY ( $x ) . ',' . padChartXY ( $y );
        $dots .= '<circle class="' . padChartSlot ( $j ) . ' pc-ring" cx="' . padChartXY ( $x ) . '" cy="' . padChartXY ( $y ) . '" r="3.5">'
               . '<title>' . padChartAttr ( "$name · {$fields [$i]}: " . padChartNumber ( $v ) ) . '</title></circle>';
      }

      $svg .= '<path class="' . padChartSlot ( $j ) . " pc-area-fill\" d=\"{$d}Z\"/>"
            . '<path class="pc-l' . ( $j + 1 ) . "\" d=\"{$d}Z\"/>" . $dots;

      $j++;

    }

    return "$svg</svg>";

  }

?>

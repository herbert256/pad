<?php

  // Steps that narrow: the funnel, and the pyramid - a triangle in layers, or the
  // population pyramid of two fields back to back.
  //
  //   {chart 'funnel',  data='shop', label='step', value='visitors'}
  //   {chart 'pyramid', data='food', label='group', value='share'}
  //   {chart 'pyramid', data='people', label='age', value='men, women'}
  //
  // funnel - the rows top to bottom as bars centred on one axis, as wide as their value
  // against the largest, joined by a light band. Each bar shows its value, and on the right
  // its share of the first step; the band between two bars the share of the step before
  // that went on.
  //
  // pyramid with one value= field - a triangle cut into layers, the first row at the top,
  // each layer's AREA its value: the rows are named beside it with value and share.
  //
  // pyramid with two value= fields - the population pyramid: the first field to the left,
  // the second to the right of a middle column that names the rows, the first row at the
  // bottom (ages listed from young to old stand as a pyramid); a value written negative,
  // as some data keeps the left side, counts as its size. Ticks below, a legend above.
  //
  // label= names the steps - else the first field that is no number; value= else the
  // first numeric field. A row without a finite number of zero or more is left out.

  function padChartFunnel ( $kind, $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $fields = padChartFields ( padTagParm ( 'value' ) ) ?: array_slice ( $numbers, 0, 1 );
    $used   = $fields;
    $label  = padChartPick ( 'label', $names, $used );
    $most   = ( $kind == 'pyramid' ) ? 2 : 1;

    if ( ! $fields )
      return '';

    if ( count ( $fields ) > $most ) {
      if ( $padCheckSyntax )
        padError ( "a $kind chart draws " . ( $most == 1 ? 'one value field' : 'one or two value fields' ) . ', it has ' . count ( $fields ) );
      $fields = array_slice ( $fields, 0, $most );
    }

    $back  = ( count ( $fields ) == 2 );
    $items = [];
    $index = 0;

    foreach ( (array) $rows as $row ) {

      $row = padChartRow ( $row );
      $index++;
      $values = [];

      foreach ( $fields as $field ) {
        $v = $row [$field] ?? NULL;
        if ( ! padChartFinite ( $v ) or ( $v < 0 and ! $back ) )
          continue 2;
        $values [] = abs ( $v + 0 );
      }

      $name     = padChartText ( $row, $label );
      $items [] = [ $name !== '' ? $name : (string) $index, $values ];

    }

    if ( ! $items )
      return '';

    $title = (string) padTagParm ( 'title', implode ( ' and ', array_map ( 'ucfirst', $fields ) ) . ( $label !== '' ? " by $label" : '' ) );

    if ( $back )
      return padChartPopulation ( $items, $fields, $title, $width, $height );

    if ( $kind == 'pyramid' )
      return padChartTriangle ( $items, $title, $width, $height );

    return padChartFunnelBars ( $items, $title, $width, $height );

  }

  function padChartFunnelBars ( $items, $title, $width, $height ) {

    $first = $items [0] [1] [0];
    $high  = max ( array_map ( fn ( $item ) => $item [1] [0], $items ) );
    $share = fn ( $v, $of ) => $of > 0 ? padChartNumber ( round ( $v / $of * 100, 1 ) ) . '%' : '';
    $desc  = [];

    if ( ! is_finite ( $high ) or $high <= 0 )
      return '';

    foreach ( $items as $i => list ( $name, $values ) )
      $desc [] = "$name: " . padChartNumber ( $values [0] )
               . ( $i ? ' (' . $share ( $values [0], $first ) . ' of the first, ' . $share ( $values [0], $items [$i - 1] [1] [0] ) . ' of the step before)' : '' );

    list ( $id, $svg ) = padChartOpen ( 'funnel', $items, $desc, $title, $width, $height );

    $labelW = 0;
    foreach ( $items as $item )
      $labelW = max ( $labelW, padChartWidth ( $item [0] ) );

    $n      = count ( $items );
    $labelW = min ( $labelW, $width * 0.25 );
    $left   = 10 + $labelW + 12;
    $right  = $width - 10 - padChartWidth ( '100%' ) - 12;
    $plotW  = max ( 10, $right - $left );
    $cx     = $left + $plotW / 2;
    $band   = ( $height - 8 ) / $n;
    $barH   = min ( 48, $band * 0.62 );
    $gap    = $band - $barH;
    $w      = fn ( $v ) => max ( 2, $v / $high * $plotW );
    $top    = fn ( $i ) => 4 + $i * $band + ( $band - $barH ) / 2;

    foreach ( $items as $i => list ( $name, $values ) ) {

      $v  = $values [0];
      $y  = $top ( $i );
      $bw = $w ( $v );

      // The band down to the next bar: its share of this step, written in it when there is
      // room, else after the share of the first on the right.

      $step = '';

      if ( $i < $n - 1 ) {

        $next = $items [$i + 1] [1] [0];
        $nw   = $w ( $next );
        $y1   = $y + $barH;
        $y2   = $top ( $i + 1 );
        $step = $share ( $next, $v );

        $svg .= '<path class="pc-c1" fill-opacity="0.18" d="M' . padChartXY ( $cx - $bw / 2 ) . ',' . padChartXY ( $y1 ) . 'L' . padChartXY ( $cx + $bw / 2 ) . ',' . padChartXY ( $y1 )
              . 'L' . padChartXY ( $cx + $nw / 2 ) . ',' . padChartXY ( $y2 ) . 'L' . padChartXY ( $cx - $nw / 2 ) . ',' . padChartXY ( $y2 ) . 'Z"><title>'
              . padChartAttr ( $items [$i + 1] [0] . ': ' . $step . ' of ' . $name ) . '</title></path>';

        if ( $gap >= 13 and $step !== '' )
          $svg .= '<text x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( ( $y1 + $y2 ) / 2 + 4 ) . '" text-anchor="middle">' . padChartAttr ( "↓ $step" ) . '</text>';

      }

      $svg .= '<rect class="pc-mark pc-c1" x="' . padChartXY ( $cx - $bw / 2 ) . '" y="' . padChartXY ( $y ) . '" width="' . padChartXY ( $bw ) . '" height="' . padChartXY ( $barH ) . '" rx="3">'
            . '<title>' . padChartAttr ( $desc [$i] ) . '</title></rect>';

      $mid   = $y + $barH / 2 + 4;
      $text  = padChartNumber ( $v );
      $textW = padChartWidth ( $text );

      if ( $textW + 10 <= $bw )
        $svg .= '<text class="pc-in" x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( $mid ) . '" text-anchor="middle">' . padChartAttr ( $text ) . '</text>';
      else
        $svg .= '<text x="' . padChartXY ( $cx + $bw / 2 + 5 ) . '" y="' . padChartXY ( $mid ) . '">' . padChartAttr ( $text ) . '</text>';

      $svg .= '<text x="' . padChartXY ( $left - 12 ) . '" y="' . padChartXY ( $mid ) . '" text-anchor="end">' . padChartAttr ( padChartFit ( $name, $labelW ) ) . '</text>'
            . '<text x="' . padChartXY ( $width - 10 ) . '" y="' . padChartXY ( $mid ) . '" text-anchor="end">' . padChartAttr ( $share ( $v, $first ) ) . '</text>';

    }

    return "$svg</svg>";

  }

  // One field: a triangle, the first row at the apex, each layer as large as its value.
  // The area above a depth grows with its square, so a layer ends at the root of the share
  // of all the layers above it and itself.

  function padChartTriangle ( $items, $title, $width, $height ) {

    $total = array_sum ( array_map ( fn ( $item ) => $item [1] [0], $items ) );

    if ( ! is_finite ( $total ) or $total <= 0 )
      return '';

    $share = fn ( $v ) => padChartNumber ( round ( $v / $total * 100, 1 ) ) . '%';
    $desc  = [];

    foreach ( $items as list ( $name, $values ) )
      $desc [] = "$name: " . padChartNumber ( $values [0] ) . ' (' . $share ( $values [0] ) . ')';

    list ( $id, $svg ) = padChartOpen ( 'pyramid', $items, $desc, $title, $width, $height );

    $top   = 10;
    $tall  = $height - 20;
    $base  = max ( 20, min ( $width * 0.5, $tall * 1.25 ) );
    $apex  = 10 + $base / 2;
    $half  = fn ( $y ) => ( $y - $top ) / $tall * $base / 2;
    $sum   = 0;
    $marks = [];

    foreach ( $items as $i => list ( $name, $values ) ) {

      $y0   = $top + $tall * sqrt ( $sum / $total );
      $sum += $values [0];
      $y1   = $top + $tall * sqrt ( min ( 1, $sum / $total ) );

      if ( $values [0] <= 0 )
        continue;

      $d = 'M' . padChartXY ( $apex - $half ( $y0 ) ) . ',' . padChartXY ( $y0 ) . 'L' . padChartXY ( $apex + $half ( $y0 ) ) . ',' . padChartXY ( $y0 )
         . 'L' . padChartXY ( $apex + $half ( $y1 ) ) . ',' . padChartXY ( $y1 ) . 'L' . padChartXY ( $apex - $half ( $y1 ) ) . ',' . padChartXY ( $y1 ) . 'Z';

      $svg .= '<path class="pc-mark pc-gap ' . padChartSlot ( $i ) . "\" d=\"$d\"><title>" . padChartAttr ( $desc [$i] ) . '</title></path>';

      $marks [] = [ $i, ( $y0 + $y1 ) / 2 ];

    }

    // The names in a column right of the triangle, at least a line apart, each joined to
    // its layer by a thin line.

    $at = array_column ( $marks, 1 );
    $m  = count ( $at );

    for ( $j = 1; $j < $m; $j++ )
      $at [$j] = max ( $at [$j], $at [$j - 1] + 16 );

    if ( $m and end ( $at ) > $height - 8 ) {
      $at [$m - 1] = $height - 8;
      for ( $j = $m - 2; $j >= 0; $j-- )
        $at [$j] = min ( $at [$j], $at [$j + 1] - 16 );
    }

    $column = 10 + $base + 24;

    foreach ( $marks as $j => list ( $i, $y ) ) {

      $edge = $apex + $half ( $y );
      $text = $items [$i] [0] . '  ' . padChartNumber ( $items [$i] [1] [0] ) . ' (' . $share ( $items [$i] [1] [0] ) . ')';

      $svg .= '<path class="pc-edge" d="M' . padChartXY ( $edge + 4 ) . ',' . padChartXY ( $y ) . 'L' . padChartXY ( $column - 16 ) . ',' . padChartXY ( $y )
            . 'L' . padChartXY ( $column - 6 ) . ',' . padChartXY ( $at [$j] ) . '"/>'
            . '<rect class="' . padChartSlot ( $i ) . '" x="' . padChartXY ( $column - 2 ) . '" y="' . padChartXY ( $at [$j] - 5 ) . '" width="10" height="10" rx="2"/>'
            . '<text x="' . padChartXY ( $column + 14 ) . '" y="' . padChartXY ( $at [$j] + 4 ) . '">' . padChartAttr ( padChartFit ( $text, $width - $column - 24 ) ) . '</text>';

    }

    return "$svg</svg>";

  }

  // Two fields back to back: the population pyramid, the first row at the bottom.

  function padChartPopulation ( $items, $fields, $title, $width, $height ) {

    $high = 0;
    foreach ( $items as $item )
      $high = max ( $high, $item [1] [0], $item [1] [1] );

    if ( ! is_finite ( $high ) )
      return '';

    $desc = [];

    foreach ( $items as list ( $name, $values ) )
      $desc [] = "$name: {$fields [0]} " . padChartNumber ( $values [0] ) . ", {$fields [1]} " . padChartNumber ( $values [1] );

    list ( $id, $svg ) = padChartOpen ( 'pyramid', [ $items, $fields ], $desc, $title, $width, $height );

    list ( $legend, $legendH ) = padChartLegend ( array_map ( 'ucfirst', $fields ), 10, 6, $width - 20 );
    $svg .= $legend;

    $labelW = 0;
    foreach ( $items as $item )
      $labelW = max ( $labelW, padChartWidth ( $item [0] ) );

    $n      = count ( $items );
    $middle = min ( $labelW, $width * 0.2 ) + 16;
    $cx     = $width / 2;
    $halfW  = max ( 10, ( $width - 20 - $middle ) / 2 - 10 );
    $ticks  = padChartTicks ( 0, $high > 0 ? $high : 1, $halfW < 200 ? 3 : 4 );
    $scale  = end ( $ticks );
    $top    = $legendH + 14;
    $bottom = $height - 22;
    $band   = ( $bottom - $top ) / $n;
    $barH   = max ( 1, min ( 28, $band * 0.8 ) );
    $leftX  = $cx - $middle / 2;
    $rightX = $cx + $middle / 2;
    $len    = fn ( $v ) => $v / $scale * $halfW;

    // The grid and ticks below, mirrored: the same scale outward on both sides.

    foreach ( $ticks as $tick )
      foreach ( [ $leftX - $len ( $tick ), $rightX + $len ( $tick ) ] as $x )
        $svg .= '<line class="pc-grid" x1="' . padChartXY ( $x ) . '" x2="' . padChartXY ( $x ) . '" y1="' . padChartXY ( $top ) . '" y2="' . padChartXY ( $bottom ) . '"/>'
              . '<text x="' . padChartXY ( $x ) . '" y="' . padChartXY ( $bottom + 15 ) . '" text-anchor="middle">' . padChartNumber ( $tick ) . '</text>';

    foreach ( $items as $i => list ( $name, $values ) ) {

      $y0 = $bottom - ( $i + 1 ) * $band + ( $band - $barH ) / 2;
      $y1 = $y0 + $barH;

      foreach ( [ 0, 1 ] as $side ) {
        $base = $side ? $rightX : $leftX;
        $end  = $side ? $rightX + $len ( $values [1] ) : $leftX - $len ( $values [0] );
        if ( $values [$side] > 0 )
          $svg .= '<path class="pc-mark ' . padChartSlot ( $side ) . '" d="' . padChartBarPath ( $y0, $y1, $base, $end, FALSE ) . '"><title>'
                . padChartAttr ( "$name · {$fields [$side]}: " . padChartNumber ( $values [$side] ) ) . '</title></path>';
      }

      $svg .= '<text x="' . padChartXY ( $cx ) . '" y="' . padChartXY ( ( $y0 + $y1 ) / 2 + 4 ) . '" text-anchor="middle">' . padChartAttr ( padChartFit ( $name, $middle - 6 ) ) . '</text>';

    }

    $svg .= '<line class="pc-axis" x1="' . padChartXY ( $leftX ) . '" x2="' . padChartXY ( $leftX ) . '" y1="' . padChartXY ( $top ) . '" y2="' . padChartXY ( $bottom ) . '"/>'
          . '<line class="pc-axis" x1="' . padChartXY ( $rightX ) . '" x2="' . padChartXY ( $rightX ) . '" y1="' . padChartXY ( $top ) . '" y2="' . padChartXY ( $bottom ) . '"/>';

    return "$svg</svg>";

  }

?>

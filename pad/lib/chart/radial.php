<?php

  // Values around a centre: the rose, and bars bent into rings.
  //
  //   {chart 'rose', data='deaths', label='month', value='disease, wounds, other'}
  //   {chart 'radialbar', data='funds', label='class', value='raised', to=5000}
  //
  // rose - Nightingale's rose, the polar area chart: a wedge per row, all of one angle,
  // clockwise in the order of the rows, the first centred on twelve o'clock - so a wind
  // rose of the sixteen compass points has north on top. A wedge's AREA is its value, so
  // its radius grows with the square root; several value= fields lie outward on one another
  // as rings, a colour each with a legend, and one field is one colour. label= names the
  // wedges around the circle; the rings of the scale are round numbers.
  //
  // radialbar - a ring per row, the first outside, each an arc from twelve o'clock clockwise
  // whose sweep is its value against to= - else round numbers past the highest value - over
  // three quarters of the circle, on a light track of its own colour. The rows are named at
  // the start of their tracks, in the quarter left free; the value is written at the end of
  // its arc, and the scale stands around the outer ring.
  //
  // A row without a finite value of zero or more is left out.

  function padChartRadial ( $kind, $rows, $width, $height ) {

    global $padCheckSyntax;

    list ( $numbers, $names ) = padChartGuess ( $rows );

    $fields = padChartFields ( padTagParm ( 'value' ) ) ?: array_slice ( $numbers, 0, 1 );
    $used   = $fields;
    $label  = padChartPick ( 'label', $names, $used );

    if ( ! $fields )
      return '';

    if ( $kind == 'radialbar' and count ( $fields ) > 1 ) {
      if ( $padCheckSyntax )
        padError ( 'a radialbar chart draws one value field, it has ' . count ( $fields ) );
      $fields = array_slice ( $fields, 0, 1 );
    }

    if ( count ( $fields ) > 8 ) {
      if ( $padCheckSyntax )
        padError ( 'the rose has ' . count ( $fields ) . ' value fields - eight at most, each its own colour' );
      $fields = array_slice ( $fields, 0, 8 );
    }

    $items = [];
    $index = 0;

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

      $name     = padChartText ( $row, $label );
      $items [] = [ $name !== '' ? $name : (string) $index, $values ];

    }

    if ( ! $items )
      return '';

    $title = (string) padTagParm ( 'title', implode ( ' and ', array_map ( 'ucfirst', $fields ) ) . ( $label !== '' ? " by $label" : '' ) );

    if ( $kind == 'rose' )
      return padChartRose ( $items, $fields, $title, $width, $height );

    return padChartRadialBar ( $items, $title, $width, $height );

  }

  function padChartRose ( $items, $fields, $title, $width, $height ) {

    $k     = count ( $fields );
    $n     = count ( $items );
    $total = max ( array_map ( fn ( $item ) => array_sum ( $item [1] ), $items ) );

    if ( ! is_finite ( $total ) )
      return '';

    $desc = [];

    foreach ( $items as list ( $name, $values ) ) {
      $parts = [];
      foreach ( $fields as $i => $field )
        $parts [] = ( $k > 1 ? "$field " : '' ) . padChartNumber ( $values [$i] );
      $desc [] = "$name: " . implode ( ', ', $parts );
    }

    list ( $id, $svg ) = padChartOpen ( 'rose', [ $items, $fields ], $desc, $title, $width, $height );

    $legendH = 0;

    if ( $k > 1 ) {
      list ( $legend, $legendH ) = padChartLegend ( array_map ( 'ucfirst', $fields ), 10, 6, $width - 20 );
      $svg .= $legend;
    }

    // The circle as large as the labels around it allow.

    $labelW = 0;
    foreach ( $items as $item )
      $labelW = max ( $labelW, padChartWidth ( $item [0] ) );

    $labelW = min ( $labelW, $width * 0.2 );
    $r      = max ( 20, min ( $width / 2 - $labelW - 14, ( $height - $legendH - 36 ) / 2 ) );
    $cx     = $width / 2;
    $cy     = $legendH + 18 + ( $height - $legendH - 36 ) / 2;
    $ticks  = padChartTicks ( 0, $total > 0 ? $total : 1, $r < 100 ? 2 : 4 );
    $scale  = end ( $ticks );
    $radius = fn ( $v ) => $r * sqrt ( max ( 0, $v ) / $scale );
    $step   = 2 * M_PI / $n;

    // The scale: a ring per tick - spaced by the square root, as the areas are - named on
    // the way up from the centre, and a spoke between the wedges.

    foreach ( $ticks as $tick )
      if ( $tick > 0 )
        $svg .= '<circle class="pc-grid" fill="none" cx="' . padChartXY ( $cx ) . '" cy="' . padChartXY ( $cy ) . '" r="' . padChartXY ( $radius ( $tick ) ) . '"/>';

    for ( $i = 0; $i < $n; $i++ )
      $svg .= '<line class="pc-grid" x1="' . padChartXY ( $cx ) . '" y1="' . padChartXY ( $cy ) . '" x2="' . padChartXY ( $cx + $r * sin ( ( $i + 0.5 ) * $step ) ) . '" y2="' . padChartXY ( $cy - $r * cos ( ( $i + 0.5 ) * $step ) ) . '"/>';

    // The wedges, ring on ring outward, a 2px surface line between them.

    foreach ( $items as $i => list ( $name, $values ) ) {

      $a0  = ( $i - 0.5 ) * $step;
      $a1  = $a0 + $step;
      $sum = 0;

      foreach ( $values as $j => $v ) {

        $inner = $radius ( $sum );
        $sum  += $v;
        $outer = $radius ( $sum );

        if ( $v <= 0 )
          continue;

        $svg .= '<path class="pc-mark pc-gap ' . ( $k > 1 ? padChartSlot ( $j ) : 'pc-c1' ) . '" d="' . padChartArc ( $cx, $cy, $inner, $outer, $a0, $a1 ) . '"><title>'
              . padChartAttr ( "$name" . ( $k > 1 ? ' · ' . $fields [$j] : '' ) . ': ' . padChartNumber ( $v ) ) . '</title></path>';

      }

    }

    // The numbers of the scale on the spoke before the first wedge, but the outer one, a
    // halo of the surface around them where they cross a wedge.

    $s = sin ( -$step / 2 );
    $c = cos ( -$step / 2 );

    foreach ( $ticks as $tick )
      if ( $tick > 0 and $tick < $scale )
        $svg .= '<text class="pc-ring" paint-order="stroke" x="' . padChartXY ( $cx + $radius ( $tick ) * $s + 3 ) . '" y="' . padChartXY ( $cy - $radius ( $tick ) * $c + 4 ) . '">' . padChartNumber ( $tick ) . '</text>';

    // The names around the circle, outside the wedges.

    foreach ( $items as $i => $item ) {
      $a      = $i * $step;
      $s      = sin ( $a );
      $c      = cos ( $a );
      $anchor = ( $s > 0.2 ) ? 'start' : ( $s < -0.2 ? 'end' : 'middle' );
      $svg   .= '<text x="' . padChartXY ( $cx + ( $r + 6 ) * $s ) . '" y="' . padChartXY ( $cy - ( $r + 6 ) * $c + 4 + ( $c < -0.5 ? 6 : 0 ) ) . "\" text-anchor=\"$anchor\">"
              . padChartAttr ( padChartFit ( $item [0], $labelW + 6 ) ) . '</text>';
    }

    return "$svg</svg>";

  }

  function padChartRadialBar ( $items, $title, $width, $height ) {

    $high  = max ( array_map ( fn ( $item ) => $item [1] [0], $items ) );
    $top   = padTagParm ( 'to' );
    $given = ( padChartFinite ( $top ) and $top > 0 );
    $ticks = padChartTicks ( 0, $given ? $top + 0 : max ( $high, 1e-9 ), 5 );
    $scale = end ( $ticks );

    // to= is the end of the scale itself, the ticks past it left out.

    if ( $given ) {
      $scale = $top + 0;
      $ticks = array_values ( array_filter ( $ticks, fn ( $tick ) => $tick < $scale - $scale * 1e-9 ) );
      $ticks [] = $scale;
    }

    $n    = count ( $items );
    $full = 1.5 * M_PI;
    $desc = [];

    foreach ( $items as list ( $name, $values ) )
      $desc [] = "$name: " . padChartNumber ( $values [0] );

    $desc [] = 'on a scale of 0 to ' . padChartNumber ( $scale );

    list ( $id, $svg ) = padChartOpen ( 'radialbar', [ $items, $scale ], $desc, $title, $width, $height );

    $r     = max ( 20, min ( $height / 2 - 18, $width / 2 - 30 ) );
    $cx    = $width / 2;
    $cy    = $height / 2 + 2;
    $hole  = $r * 0.22;
    $band  = ( $r - $hole ) / $n;
    $thick = min ( 28, $band * 0.72 );
    $at    = fn ( $v ) => min ( $v, $scale ) / $scale * $full;

    // The scale: a spoke per tick across the rings, its number past the outer one.

    foreach ( $ticks as $tick ) {
      $a      = $at ( $tick );
      $s      = sin ( $a );
      $c      = cos ( $a );
      $svg   .= '<line class="pc-grid" x1="' . padChartXY ( $cx + $hole * $s ) . '" y1="' . padChartXY ( $cy - $hole * $c ) . '" x2="' . padChartXY ( $cx + ( $r + 3 ) * $s ) . '" y2="' . padChartXY ( $cy - ( $r + 3 ) * $c ) . '"/>';
      $anchor = ( $s > 0.3 ) ? 'start' : ( $s < -0.3 ? 'end' : 'middle' );
      $svg   .= '<text x="' . padChartXY ( $cx + ( $r + 7 ) * $s ) . '" y="' . padChartXY ( $cy - ( $r + 7 ) * $c + 4 + ( $c < -0.5 ? 5 : 0 ) - ( $c > 0.5 ? 1 : 0 ) ) . "\" text-anchor=\"$anchor\">"
            . padChartNumber ( $tick ) . '</text>';
    }

    // A ring per row, the first outside: the light track, the arc, the name before it and
    // the value after it.

    foreach ( $items as $i => list ( $name, $values ) ) {

      $v     = $values [0];
      $mid   = $r - ( $i + 0.5 ) * $band;
      $inner = $mid - $thick / 2;
      $outer = $mid + $thick / 2;
      $slot  = padChartSlot ( $i );
      $sweep = $at ( $v );
      $tip   = padChartAttr ( "$name: " . padChartNumber ( $v ) . ( $v > $scale ? ' (past the scale)' : '' ) );

      $svg .= '<path class="' . $slot . '" fill-opacity="0.15" d="' . padChartArc ( $cx, $cy, $inner, $outer, 0, $full ) . '"/>';

      if ( $sweep > 0 )
        $svg .= '<path class="pc-mark ' . $slot . '" d="' . padChartArc ( $cx, $cy, $inner, $outer, 0, max ( $sweep, 0.004 ) ) . "\"><title>$tip</title></path>";

      $svg .= '<text x="' . padChartXY ( $cx - 6 ) . '" y="' . padChartXY ( $cy - $mid + 4 ) . '" text-anchor="end">'
            . padChartAttr ( padChartFit ( $name, $cx - 10 ) ) . '</text>';

      // The value past the end of the arc while it fits before the free quarter, else
      // inside the end of the arc, light on the fill.

      $text  = padChartNumber ( $v );
      $textW = padChartWidth ( $text );
      $past  = $sweep + ( $textW + 8 ) / $mid;

      if ( $past < $full - 0.05 ) {
        $a    = $sweep + ( $textW / 2 + 5 ) / $mid;
        $svg .= '<text x="' . padChartXY ( $cx + $mid * sin ( $a ) ) . '" y="' . padChartXY ( $cy - $mid * cos ( $a ) + 4 ) . '" text-anchor="middle">' . padChartAttr ( $text ) . '</text>';
      } elseif ( $thick >= 12 ) {
        $a    = $sweep - ( $textW / 2 + 5 ) / $mid;
        $svg .= '<text class="pc-in" x="' . padChartXY ( $cx + $mid * sin ( $a ) ) . '" y="' . padChartXY ( $cy - $mid * cos ( $a ) + 4 ) . '" text-anchor="middle">' . padChartAttr ( $text ) . '</text>';
      }

    }

    return "$svg</svg>";

  }

?>

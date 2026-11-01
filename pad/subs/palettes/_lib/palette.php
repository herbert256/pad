<?php

  // The palettes subsystem: colour scales for charts and anything else.
  //
  //   {palette 'viridis', steps=7/}                     swatches, each with its hex value
  //   padPalette ( 'viridis', 7 )                       the colours, '#rrggbb' each
  //   {chart 'bar', data='sales', palette='okabe-ito'/} a chart in the palette's colours
  //
  // A palette is a file of subs/palettes/types/ - its name, its type and its colours - and the
  // files there are the palettes there are (padKinds, lib/subsystem.php); no tag type: a palette
  // is the parameter of {palette}, the option palette= of {chart} and the first argument of
  // padPalette. Three types:
  //
  //   sequential   a scale from low to high - viridis, magma, plasma, inferno and cividis from
  //                matplotlib's published 256-colour tables, the ColorBrewer blues, greens,
  //                oranges and greys of nine classes
  //   diverging    two scales meeting in a light middle - ColorBrewer's rdbu, brbg and piyg of
  //                eleven classes
  //   categorical  colours to tell things apart, no order - okabe-ito and Paul Tol's bright,
  //                muted and vibrant, which colour-blind eyes tell apart, ColorBrewer's pastel
  //                and PAD's own earth
  //
  // Steps: a sequential or diverging palette is sampled evenly from its first colour to its
  // last - the colours between two of its own made by mixing them in sRGB, so nine steps of
  // a nine-class scale are the scale itself and seven of a 256-colour table are seven of its
  // colours, give or take a rounding - and one step is the middle. A categorical palette
  // gives its colours in its order, and past its last starts again: an eighth of a palette
  // of seven is its first. Without steps, a categorical palette gives all its colours, the
  // others nine.
  //
  // Included on use - by {palette}, by a chart given palette= and by padPalette (lib/palette.php).

  function padPaletteTypes () {

    return padKinds ( 'palettes' );

  }

  function padPaletteType ( $name ) {

    return padKind ( 'palettes', $name );

  }

  // What a palette's file says: [ 'name', 'type', 'source', 'colors' ] - kept for the request.

  function padPaletteData ( $name ) {

    static $memo = [];

    if ( ! padPaletteType ( $name ) )
      return NULL;

    return $memo [$name] ??= include PAD . "subs/palettes/types/$name.php";

  }

  // The default number of steps of a palette.

  function padPaletteSteps ( $name ) {

    $data = padPaletteData ( $name );

    return ( $data ['type'] == 'categorical' ) ? count ( $data ['colors'] ) : 9;

  }

  // The colours of a palette in $steps steps, '#rrggbb' each - see the top of this file.

  function padPaletteColors ( $name, $steps = NULL ) {

    $data = padPaletteData ( $name );

    if ( ! $data )
      return [];

    $steps  = $steps ?? padPaletteSteps ( $name );
    $colors = $data ['colors'];
    $list   = [];

    if ( $data ['type'] == 'categorical' ) {
      for ( $i = 0; $i < $steps; $i++ )
        $list [] = $colors [ $i % count ( $colors ) ];
      return $list;
    }

    if ( $steps == 1 )
      return [ padPaletteAt ( $colors, 0.5 ) ];

    for ( $i = 0; $i < $steps; $i++ )
      $list [] = padPaletteAt ( $colors, $i / ( $steps - 1 ) );

    return $list;

  }

  // The colour at $t, from 0 the first to 1 the last: between two colours of the list, the
  // two mixed in proportion, each channel rounded.

  function padPaletteAt ( $colors, $t ) {

    $last = count ( $colors ) - 1;
    $at   = max ( 0, min ( 1, $t ) ) * $last;
    $i    = (int) floor ( $at );

    if ( $i >= $last )
      return $colors [$last];

    $f = $at - $i;
    $a = padPaletteRgb ( $colors [$i] );
    $b = padPaletteRgb ( $colors [ $i + 1 ] );

    return sprintf ( '#%02x%02x%02x', (int) round ( $a [0] + ( $b [0] - $a [0] ) * $f ),
                                     (int) round ( $a [1] + ( $b [1] - $a [1] ) * $f ),
                                     (int) round ( $a [2] + ( $b [2] - $a [2] ) * $f ) );

  }

  function padPaletteRgb ( $hex ) {

    return [ hexdec ( substr ( $hex, 1, 2 ) ), hexdec ( substr ( $hex, 3, 2 ) ), hexdec ( substr ( $hex, 5, 2 ) ) ];

  }

  // The relative luminance of a colour and the contrast of two, as WCAG 2 defines them.

  function padPaletteLuminance ( $hex ) {

    $lin = fn ( $c ) => ( ( $c /= 255 ) <= 0.04045 ) ? $c / 12.92 : ( ( $c + 0.055 ) / 1.055 ) ** 2.4;

    [ $r, $g, $b ] = padPaletteRgb ( $hex );

    return 0.2126 * $lin ( $r ) + 0.7152 * $lin ( $g ) + 0.0722 * $lin ( $b );

  }

  function padPaletteContrast ( $one, $two ) {

    $a = padPaletteLuminance ( $one );
    $b = padPaletteLuminance ( $two );

    return ( max ( $a, $b ) + 0.05 ) / ( min ( $a, $b ) + 0.05 );

  }

  // The text that reads best on a colour: black or white, whichever contrasts more.

  function padPaletteInk ( $hex ) {

    return ( padPaletteContrast ( $hex, '#000000' ) >= padPaletteContrast ( $hex, '#ffffff' ) ) ? '#000000' : '#ffffff';

  }

  // The colours of a chart in a palette: the custom properties its svg carries, for a chart
  // that uses the first $n of the eight categorical slots (pc-c1 to pc-c8, --pad-chart-series
  // the first) - those $n alone. A categorical palette gives the slots its colours in its
  // order. A sequential or diverging palette spreads the slots over its whole length - the
  // middles of $n equal parts, so neither end, which is near the surface for some, is a
  // slot - and gives the seven steps of the heatmap's ramp (--pad-chart-heat-0 to -6) from
  // its first colour to its last; a categorical palette leaves the ramp as it is.

  function padPaletteChart ( $name, $n ) {

    $data = padPaletteData ( $name );

    if ( ! $data )
      return '';

    $n     = max ( 1, min ( 8, (int) $n ) );
    $slots = [];
    $style = '';

    if ( $data ['type'] == 'categorical' )
      $slots = padPaletteColors ( $name, $n );
    else
      for ( $i = 0; $i < $n; $i++ )
        $slots [] = padPaletteAt ( $data ['colors'], ( $i + 0.5 ) / $n );

    foreach ( $slots as $i => $color )
      $style .= '--pad-chart-' . ( $i ? $i + 1 : 'series' ) . ":$color;";

    // The ramp, and the ink of a value written on each step of it - black or white, which
    // reads better (padPaletteInk): a scale's dark end may be its first step, where the
    // default ramp has its lightest.

    if ( $data ['type'] != 'categorical' )
      for ( $step = 0; $step <= 6; $step++ )
        $style .= "--pad-chart-heat-$step:" . padPaletteAt ( $data ['colors'], $step / 6 ) . ';';

    if ( $data ['type'] != 'categorical' )
      for ( $step = 0; $step <= 6; $step++ )
        $style .= "--pad-chart-heat-ink-$step:" . padPaletteInk ( padPaletteAt ( $data ['colors'], $step / 6 ) ) . ';';

    return rtrim ( $style, ';' );

  }

?>

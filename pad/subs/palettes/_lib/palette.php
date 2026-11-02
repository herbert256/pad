<?php

  // The palettes subsystem: colour scales for charts and anything else.
  //
  //   {widget:palette 'viridis', steps=7/}                     swatches, each with its hex value
  //   padPalette ( 'viridis', 7 )                       the colours, '#rrggbb' each
  //   {chart:bar data='sales', palette='okabe-ito'/} a chart in the palette's colours
  //
  // A palette is a file of subs/palettes/types/ - its name, its type and its colours - and the
  // files there are the palettes there are (padKinds, lib/subsystem.php); no tag type: a palette
  // is the parameter of {widget:palette}, the option palette= of {chart:bar} and the first argument of
  // padPalette. Four types:
  //
  //   sequential   a scale from low to high - viridis, magma, plasma, inferno and cividis from
  //                matplotlib's published 256-colour tables, the ColorBrewer blues, greens,
  //                oranges and greys of nine classes and its ylgnbu and ylorrd, Crameri's
  //                batlow, Google's turbo
  //   diverging    two scales meeting in a light middle - ColorBrewer's rdbu, brbg, piyg, rdylbu
  //                and puor of eleven classes, Crameri's vik
  //   cyclic       a scale that goes round, for what does - an angle, a direction, the hour,
  //                the month: its colours run once round a circle, its last next to its first
  //                again - Crameri's romaO
  //   categorical  colours to tell things apart, no order - okabe-ito and Paul Tol's bright,
  //                muted and vibrant, which colour-blind eyes tell apart, ColorBrewer's pastel,
  //                dark2 and paired, PAD's own earth and glasbey's 32 for many categories
  //
  // Steps: a sequential or diverging palette is sampled evenly from its first colour to its
  // last - the colours between two of its own made by mixing them in sRGB, so nine steps of
  // a nine-class scale are the scale itself and seven of a 256-colour table are seven of its
  // colours, give or take a rounding - and one step is the middle. A cyclic palette is
  // sampled evenly round its circle from its first colour on, its last step short of the
  // first again - twelve steps are the twelve months, none of them twice, where sampled to
  // its end its last colour would be its first once more - and one step is its first; its
  // colours are as many even steps of the circle, so four steps are its quarters and 256 of
  // romaO its table (padPaletteRound). A categorical palette gives its colours in its order,
  // and past its last starts again: an eighth of a palette of seven is its first. Without
  // steps, a categorical palette gives all its colours, a cyclic one twelve, the others nine.
  //
  // Included on use - by {widget:palette}, by a chart given palette= and by padPalette (lib/palette.php).

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

  // Suitability is an explicit rendering recommendation, not an accessibility certification.
  function padPaletteMetadata($name){
    $data=padPaletteData($name);if(!$data)return NULL;$type=$data['type'];$category=$type==='categorical'?count($data['colors']):NULL;
    $samples=padPaletteColors($name,$type==='categorical'?$category:9);$suitability=[];
    foreach(['light'=>'#ffffff','dark'=>'#111827'] as $scheme=>$background){$minimum=min(array_map(fn($hex)=>padPaletteContrast($hex,$background),$samples));$suitability[$scheme]=['minimumMarkContrast'=>round($minimum,2),'outlineRecommended'=>$minimum<3,'guidance'=>$minimum<3?'Use contrasting outlines and text labels.':'Use text labels; color is supplementary.'];}
    return ['type'=>$type,'recommendedCategories'=>$category,'light'=>$suitability['light'],'dark'=>$suitability['dark'],'usage'=>match($type){'categorical'=>'Unordered distinct categories','sequential'=>'Ordered low-to-high magnitude','diverging'=>'Signed distance from a meaningful center','cyclic'=>'A wrapping periodic domain'}];
  }
  function padPaletteUsage($name,$type,$categories=0,$scheme='light'){
    $meta=padPaletteMetadata($name);if(!$meta)return ['Unknown palette'];$issues=[];
    if(!in_array($type,['categorical','sequential','diverging','cyclic'],TRUE))$issues[]='Unknown scale type';elseif($meta['type']!==$type)$issues[]='This '.$meta['type'].' palette does not represent a '.$type.' scale';
    if(!is_int($categories)||$categories<0)$issues[]='Category count must be a nonnegative integer';elseif($type==='categorical'&&$meta['recommendedCategories']!==NULL&&$categories>$meta['recommendedCategories'])$issues[]='Category count exceeds the distinct palette colors';
    if(!in_array($scheme,['light','dark'],TRUE))$issues[]='Scheme must be light or dark';
    return $issues;
  }
  function padPaletteSelect($name,$type,$steps=NULL,$scheme='light'){
    if($steps!==NULL&&(!is_int($steps)||$steps<1||$steps>256))throw new InvalidArgumentException('Palette steps must be 1 to 256');
    $issues=padPaletteUsage($name,$type,$type==='categorical'?($steps??0):0,$scheme);if($issues)throw new InvalidArgumentException(implode('; ',$issues));return padPaletteColors($name,$steps);
  }

  // The default number of steps of a palette.

  function padPaletteSteps ( $name ) {

    $data = padPaletteData ( $name );

    if ( $data ['type'] == 'categorical' )
      return count ( $data ['colors'] );

    return ( $data ['type'] == 'cyclic' ) ? 12 : 9;

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

    if ( $data ['type'] == 'cyclic' ) {
      for ( $i = 0; $i < $steps; $i++ )
        $list [] = padPaletteRound ( $colors, $i / $steps );
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

    return padPaletteMix ( $colors [$i], $colors [ $i + 1 ], $at - $i );

  }

  // The colour at $t round the circle of a cyclic palette, from 0 its first colour once round
  // to 1 its first again: its colours are as many even steps of the circle, its last one step
  // short of its first, so $t is at entry $t times their count, and past its last colour the
  // last and the first are mixed - where the table was read as closed, its last colour at 1,
  // and romaO's quarters fell between two of its colours.

  function padPaletteRound ( $colors, $t ) {

    $count = count ( $colors );
    $at    = ( $t - floor ( $t ) ) * $count;
    $i     = (int) floor ( $at );

    return padPaletteMix ( $colors [ $i % $count ], $colors [ ( $i + 1 ) % $count ], $at - $i );

  }

  // Two colours mixed in sRGB, $f of the way from the first to the second, each channel rounded.

  function padPaletteMix ( $one, $two, $f ) {

    $a = padPaletteRgb ( $one );
    $b = padPaletteRgb ( $two );

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
  // order. A sequential, diverging or cyclic palette spreads the slots over its whole length -
  // the middles of $n equal parts, so neither end, which is near the surface for some, is a
  // slot; one slot of a diverging palette, whose middle is pale, the middle of its first arm
  // instead - and gives the seven steps of the heatmap's ramp (--pad-chart-heat-0 to -6) from
  // its first colour to its last, a cyclic one seven steps round its circle, the last short
  // of the first, so the lowest and the highest step are neighbours and never one colour; a
  // categorical palette leaves the ramp as it is.

  function padPaletteChart ( $name, $n ) {

    $data = padPaletteData ( $name );

    if ( ! $data )
      return '';

    $n     = max ( 1, min ( 8, (int) $n ) );
    $slots = [];
    $style = '';

    $at = ( $data ['type'] == 'cyclic' ) ? 'padPaletteRound' : 'padPaletteAt';

    // One slot of a diverging palette is the middle of its first arm, 0.2: the middle of the
    // whole, which one slot of a scale is, is the pale part where its two arms meet - a
    // ridgeline, an ecdf of one group or a bar of one series in rdylbu was drawn in #ffffbf,
    // a contrast of 1.01 with the light surface.

    if ( $data ['type'] == 'categorical' )
      $slots = padPaletteColors ( $name, $n );
    elseif ( $n == 1 and $data ['type'] == 'diverging' )
      $slots [] = padPaletteAt ( $data ['colors'], 0.2 );
    else
      for ( $i = 0; $i < $n; $i++ )
        $slots [] = $at ( $data ['colors'], ( $i + 0.5 ) / $n );

    foreach ( $slots as $i => $color )
      $style .= '--pad-chart-' . ( $i ? $i + 1 : 'series' ) . ":$color;";

    // The ramp, and the ink of a value written on each step of it - black or white, which
    // reads better (padPaletteInk): a scale's dark end may be its first step, where the
    // default ramp has its lightest.

    $round = ( $data ['type'] == 'cyclic' ) ? 7 : 6;

    if ( $data ['type'] != 'categorical' )
      for ( $step = 0; $step <= 6; $step++ )
        $style .= "--pad-chart-heat-$step:" . $at ( $data ['colors'], $step / $round ) . ';';

    if ( $data ['type'] != 'categorical' )
      for ( $step = 0; $step <= 6; $step++ )
        $style .= "--pad-chart-heat-ink-$step:" . padPaletteInk ( $at ( $data ['colors'], $step / $round ) ) . ';';

    return rtrim ( $style, ';' );

  }

?>

<?php

  // epicycles - a drawing traced again by circles riding on circles, its Fourier series: the
  // drawing is walked at an even pace, sampled at points= places, and every sample taken as a
  // number of the complex plane; the discrete Fourier transform of them is a circle for every
  // whole number of turns, each as large and as far round as its coefficient, and the circles
  // turning together, each on the rim of the one before, lead a pen along the drawing. A few
  // of the largest already give its shape, more bring its corners - a square never quite, the
  // overshoot at a jump that Gibbs explained - and the error says how far off the pen still
  // is: the root mean square distance from the samples, as a share of the drawing's own size.
  // Where the spirograph rolls gears, this takes a curve apart. Draw a closed curve with the
  // pointer to take it apart instead; the arrow keys add and take away circles. shape= heart
  // (unless given), star, square or trefoil; circles= how many are used (30, 1 to 1023, at
  // most one fewer than the points); points= the samples (256, 16 to 1024); order= size, the
  // largest circles first (unless given), or frequency, one turn, minus one, two, minus two
  // ...; speed= how fast the pen goes round (3, 1 to 10). A drawing of your own stands
  // between the tags as an SVG polyline's points, x,y pairs - y counted down - and is closed.
  //
  //   {epicycles shape='square', circles=15/}
  //   {epicycles}
  //     0,0 40,10 80,0 70,40 80,80 40,70 0,80 10,40
  //   {/epicycles}

  // A drawing of your own: x,y pairs between spaces or new lines, as in the points of
  // an SVG polyline - three of them or more, at most 2000.

  $padEpiOwn = [];

  foreach ( padToyLines () as $padEpiLine )
    foreach ( preg_split ( '/\s+/', trim ( $padEpiLine ) ) as $padEpiPair ) {
      if ( ! preg_match ( '/^(-?(?:\d+\.?\d*|\.\d+)),(-?(?:\d+\.?\d*|\.\d+))$/D', $padEpiPair, $padEpiMatch ) ) {
        if ( $GLOBALS ['padCheckSyntax'] )
          padError ( "the " . padToyWhat () . " has a point '" . padMakeSafe ( $padEpiPair, 30 ) . "' - two numbers x,y, as in the points of an SVG polyline" );
        $padEpiOwn = [];
        break 2;
      }
      $padEpiOwn [] = [ $padEpiMatch [1] + 0, $padEpiMatch [2] + 0 ];
    }

  if ( padToySource () !== '' and ( count ( $padEpiOwn ) < 3 or count ( $padEpiOwn ) > 2000 ) ) {
    if ( $GLOBALS ['padCheckSyntax'] and ( $padEpiOwn or count ( padToyLines () ) ) )
      padError ( "the " . padToyWhat () . " drawing of your own has " . count ( $padEpiOwn ) . " points - three to 2000" );
    $padEpiOwn = [];
  }

  $padEpiPoints = padToyInt ( 'points', 256, 16, 1024 );
  $padEpiCircles = padToyInt ( 'circles', 30, 1, 1023 );

  if ( $padEpiCircles >= $padEpiPoints ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " has $padEpiCircles circles of $padEpiPoints points - at most one fewer than the points" );
    $padEpiCircles = $padEpiPoints - 1;
  }

  // The drawing of your own is the one shown unless shape= picks another.

  return padToy ( 'epicycles', 'Epicycles', [
    'shape'   => padToyChoice ( 'shape', $padEpiOwn ? 'yours' : 'heart', array_merge ( $padEpiOwn ? [ 'yours' ] : [], [ 'heart', 'star', 'square', 'trefoil' ] ) ),
    'circles' => $padEpiCircles,
    'points'  => $padEpiPoints,
    'order'   => padToyChoice ( 'order', 'size', [ 'size', 'frequency' ] ),
    'speed'   => padToyInt ( 'speed', 3, 1, 10 ),
    'drawing' => $padEpiOwn
  ] );

?>

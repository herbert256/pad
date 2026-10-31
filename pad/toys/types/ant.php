<?php

  // ant - Langton's ant and its many-coloured cousins, the turmites: an ant walks a grid of
  // squares, turns on each square by the colour it finds there, steps the square's colour on
  // and moves ahead. With two colours it scribbles chaos for ten thousand steps and then
  // builds a highway; other rules draw symmetric shapes, squares and triangles. Click to put
  // down another ant. rule= the turn for each colour, R right, L left, N none and U about -
  // RL unless given, the classic ant; ants= how many start (1, up to 8); size= the squares a
  // side (160, 40 to 400); speed= the steps a second (1000, 1 to 50000).
  //
  //   {ant rule='LLRR', ants=2/}

  $padAntRule = strtoupper ( trim ( padParmText ( padTagParm ( 'rule', 'RL' ) ) ) );

  if ( ! preg_match ( '/^[LRNU]{2,16}$/D', $padAntRule ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " has no rule '" . padMakeSafe ( $padAntRule, 20 ) . "' - 2 to 16 of L, R, N and U, a turn for each colour: RL" );
    $padAntRule = 'RL';
  }

  return padToy ( 'ant', "Langton's ant", [
    'rule'  => $padAntRule,
    'ants'  => padToyInt ( 'ants', 1, 1, 8 ),
    'size'  => padToyInt ( 'size', 160, 40, 400 ),
    'speed' => padToyInt ( 'speed', 1000, 1, 50000 )
  ] );

?>

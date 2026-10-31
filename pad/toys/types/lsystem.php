<?php

  // lsystem - plants and fractal curves grown from a few rewriting rules, Lindenmayer's
  // systems: a word is rewritten over and over, every letter by its rule, and a turtle draws
  // the result - F a line forward, + and - a turn, [ and ] a branch. Watch a fern grow, a
  // Koch snowflake, a dragon curve fold, Hilbert's curve fill its square; the iterations
  // slider rewrites once more, a drag sideways bends the angle, and wind sways a plant. The
  // first value names the system - plant unless given; iterations= how often it is
  // rewritten (each system its own, 1 to 16); angle= the turn in degrees instead of the
  // system's own. A system of your own stands between the tags: axiom: the word to start
  // with, angle: the turn, and a line per rule - X: F+[[X]-X]-F[-FX]+X.
  //
  //   {lsystem 'dragon', iterations=14/}
  //   {lsystem}
  //     axiom: X
  //     X: F[+X]F[-X]+X
  //     F: FF
  //     angle: 20
  //   {/lsystem}

  // The systems to pick from: the word to start with, the rules, the turn, how often they are
  // rewritten unless iterations= says, and the most that still draws in a moment. F and G
  // draw a line, f moves without one.

  $padLsysSystems = [
    'plant'      => [ 'axiom' => 'X', 'rules' => [ 'X' => 'F+[[X]-X]-F[-FX]+X', 'F' => 'FF' ], 'angle' => 25, 'iterations' => 5, 'most' => 7 ],
    'bush'       => [ 'axiom' => 'F', 'rules' => [ 'F' => 'FF+[+F-F-F]-[-F+F+F]' ], 'angle' => 22.5, 'iterations' => 4, 'most' => 5 ],
    'weed'       => [ 'axiom' => 'X', 'rules' => [ 'X' => 'F[+X][-X]FX', 'F' => 'FF' ], 'angle' => 25.7, 'iterations' => 6, 'most' => 8 ],
    'koch'       => [ 'axiom' => 'F--F--F', 'rules' => [ 'F' => 'F+F--F+F' ], 'angle' => 60, 'iterations' => 4, 'most' => 6 ],
    'island'     => [ 'axiom' => 'F-F-F-F', 'rules' => [ 'F' => 'F-F+F+FF-F-F+F' ], 'angle' => 90, 'iterations' => 2, 'most' => 4 ],
    'dragon'     => [ 'axiom' => 'FX', 'rules' => [ 'X' => 'X+YF+', 'Y' => '-FX-Y' ], 'angle' => 90, 'iterations' => 12, 'most' => 16 ],
    'levy'       => [ 'axiom' => 'F', 'rules' => [ 'F' => '+F--F+' ], 'angle' => 45, 'iterations' => 12, 'most' => 16 ],
    'sierpinski' => [ 'axiom' => 'F', 'rules' => [ 'F' => 'G-F-G', 'G' => 'F+G+F' ], 'angle' => 60, 'iterations' => 7, 'most' => 9 ],
    'hilbert'    => [ 'axiom' => 'X', 'rules' => [ 'X' => '+YF-XFX-FY+', 'Y' => '-XF+YFY+FX-' ], 'angle' => 90, 'iterations' => 5, 'most' => 7 ],
    'gosper'     => [ 'axiom' => 'F', 'rules' => [ 'F' => 'F-G--G+F++FF+G-', 'G' => '+F-GG--G-F++F+G' ], 'angle' => 60, 'iterations' => 4, 'most' => 5 ],
    'crystal'    => [ 'axiom' => 'F+F+F+F', 'rules' => [ 'F' => 'FF+F++F+F' ], 'angle' => 90, 'iterations' => 3, 'most' => 5 ],
  ];

  // A system of your own: axiom and angle, and a rule a line - one letter, a colon, what it
  // becomes, in letters, + - [ ] and |.

  $padLsysOwn = [ 'axiom' => '', 'rules' => [], 'angle' => 90, 'iterations' => 5, 'most' => 12 ];

  foreach ( padToyLines () as $padLsysLine ) {
    if ( preg_match ( '/^axiom\s*:\s*([A-Za-z+\-\[\]|]+)$/iD', $padLsysLine, $padLsysMatch ) )
      $padLsysOwn ['axiom'] = $padLsysMatch [1];
    elseif ( preg_match ( '/^angle\s*:\s*(\d+(?:\.\d+)?)$/iD', $padLsysLine, $padLsysMatch ) and $padLsysMatch [1] > 0 and $padLsysMatch [1] < 360 )
      $padLsysOwn ['angle'] = $padLsysMatch [1] + 0;
    elseif ( preg_match ( '/^([A-Za-z])\s*[:=]\s*([A-Za-z+\-\[\]|]*)$/D', $padLsysLine, $padLsysMatch ) )
      $padLsysOwn ['rules'] [ $padLsysMatch [1] ] = $padLsysMatch [2];
    elseif ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " has a line '" . padMakeSafe ( $padLsysLine, 40 ) . "' - axiom: the word, angle: the turn, or a rule, X: F[+X]-X" );
  }

  if ( padToySource () !== '' and ( $padLsysOwn ['axiom'] === '' or ! $padLsysOwn ['rules'] ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " of your own needs an axiom: line and a rule or more" );
  } elseif ( $padLsysOwn ['axiom'] !== '' )
    $padLsysSystems = [ 'yours' => $padLsysOwn ] + $padLsysSystems;

  $padLsysName = strtolower ( trim ( (string) padToyValue ( '' ) ) );

  if ( $padLsysName === '' )
    $padLsysName = isset ( $padLsysSystems ['yours'] ) ? 'yours' : 'plant';
  elseif ( ! isset ( $padLsysSystems [$padLsysName] ) ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " has no system '" . padMakeSafe ( $padLsysName, 20 ) . "' - " . implode ( ', ', array_keys ( $padLsysSystems ) ) );
    $padLsysName = 'plant';
  }

  $padLsysIterations = padToyInt ( 'iterations', $padLsysSystems [$padLsysName] ['iterations'], 1, 16 );

  if ( $padLsysIterations > $padLsysSystems [$padLsysName] ['most'] ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " $padLsysName grows too large after $padLsysIterations iterations - at most " . $padLsysSystems [$padLsysName] ['most'] );
    $padLsysIterations = $padLsysSystems [$padLsysName] ['most'];
  }

  return padToy ( 'lsystem', 'L-system', [
    'system'     => $padLsysName,
    'iterations' => $padLsysIterations,
    'angle'      => padToyNumber ( 'angle', $padLsysSystems [$padLsysName] ['angle'], 1, 180 ),
    'systems'    => $padLsysSystems
  ] );

?>

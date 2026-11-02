<?php

  // spirograph - a toothed wheel rolls round inside a ring, a pen in one of its holes, and
  // draws the curve - round and round until the teeth meet again where they began. Change
  // the wheel, the hole and the ring, roll it outside the ring instead, and lay one drawing
  // over the last in another colour. ring= the teeth of the ring (96 unless given, 40 to
  // 160); wheel= the teeth of the wheel (52, 10 to 150 - fewer than the ring when it rolls
  // inside); pen= how far out the hole is, in percent of the wheel (70, 5 to 100); outside
  // rolls the wheel round the outside of the ring.
  //
  //   {toy:spirograph ring=105, wheel=63, pen=80/}

  $padSpiroRing  = padToyInt ( 'ring', 96, 40, 160 );
  $padSpiroWheel = padToyInt ( 'wheel', 52, 10, 150 );
  $padSpiroOut   = padToyFlag ( 'outside' );

  if ( ! $padSpiroOut and $padSpiroWheel >= $padSpiroRing ) {
    if ( $GLOBALS ['padCheckSyntax'] )
      padError ( "the " . padToyWhat () . " wheel of $padSpiroWheel teeth does not fit inside the ring of $padSpiroRing - give it fewer, or roll it outside" );
    $padSpiroWheel = (int) floor ( $padSpiroRing / 2 );
  }

  return padToy ( 'spirograph', 'Spirograph', [
    'ring'    => $padSpiroRing,
    'wheel'   => $padSpiroWheel,
    'pen'     => padToyInt ( 'pen', 70, 5, 100 ),
    'outside' => $padSpiroOut
  ] );

?>

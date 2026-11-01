<?php

  // siteswap - a juggling pattern in siteswap notation, each throw the number of beats until the
  // ball is thrown again - 3 the cascade, 531, 441, 97531, b for 11 - drawn as a ladder diagram:
  // the right and the left hand as two rails along the time, every throw from the beat it leaves
  // to the beat it lands, an arc back into the same hand or a line across to the other,
  // coloured by its orbit, the throws written under their beats. Multiplex throws several balls
  // from one hand at once, [33]3; a synchronous pattern throws with both hands at once,
  // (4,2x)(2x,4), x to cross, * for the same again with the hands swapped. A pattern no juggler
  // can throw - 543, two balls landing at once - is named. title= is the accessible name; a
  // screen reader hears the number of balls, the period, whether it starts from the ground state
  // and what every throw does.
  //
  //   {siteswap '531'/}
  //   {siteswap '(4,2x)(2x,4)'/}

  if ( trim ( $padNotationText ) === '' )
    return '';

  $padSiteswapRead = padSiteswapRead ( $padNotationText );

  if ( is_string ( $padSiteswapRead ) ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has $padSiteswapRead" );
    return '';
  }

  list ( $padSiteswapSync, $padSiteswapPattern ) = $padSiteswapRead;
  list ( $padSiteswapBalls, $padSiteswapWrong )  = padSiteswapCheck ( $padSiteswapSync, $padSiteswapPattern );

  $padSiteswapText = padSiteswapText ( $padSiteswapSync, $padSiteswapPattern );

  if ( $padSiteswapWrong !== '' ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has '" . padNotationShow ( $padSiteswapText ) . "', $padSiteswapWrong" );
    return '';
  }

  list ( $padSiteswapOrbit, $padSiteswapOrbits ) = padSiteswapOrbits ( $padSiteswapSync, $padSiteswapPattern );

  $padSiteswapPeriod = count ( $padSiteswapPattern ) / ( $padSiteswapSync ? 2 : 1 );
  $padSiteswapState  = $padSiteswapSync ? '' : ( padSiteswapGround ( $padSiteswapPattern, $padSiteswapBalls ) ? ', ground state' : ', excited state' );
  $padSiteswapTitle  = padNotationOption ( 'title' );

  if ( $padSiteswapTitle === '' )
    $padSiteswapTitle = "Siteswap $padSiteswapText";

  // The orbits in words: the throws each carries round and its balls.

  $padSiteswapSaid = [];

  foreach ( $padSiteswapOrbits as $padSiteswapOne ) {
    $padSiteswapThrows = array_map ( function ( $key ) use ( $padSiteswapPattern ) { list ( $beat, $index ) = explode ( ':', $key ); return $padSiteswapPattern [$beat] [$index]; }, $padSiteswapOne );
    $padSiteswapCarry  = array_sum ( array_column ( $padSiteswapThrows, 0 ) ) / count ( $padSiteswapPattern );
    $padSiteswapNames  = array_column ( $padSiteswapThrows, 3 );
    $padSiteswapSaid [] = ( count ( $padSiteswapNames ) > 1 ? implode ( ', ', array_slice ( $padSiteswapNames, 0, -1 ) ) . ' and ' . end ( $padSiteswapNames ) . ' carry' : $padSiteswapNames [0] . ' carries' )
                        . " $padSiteswapCarry " . ( $padSiteswapCarry == 1 ? 'ball' : 'balls' );
  }

  $padSiteswapDesc = "$padSiteswapBalls " . ( $padSiteswapBalls == 1 ? 'ball' : 'balls' ) . ', period ' . $padSiteswapPeriod
                   . ( $padSiteswapSync ? ', synchronous' : '' ) . "$padSiteswapState. "
                   . 'Throws: ' . padSiteswapWords ( $padSiteswapSync, $padSiteswapPattern ) . '. '
                   . ( count ( $padSiteswapOrbits ) == 1 ? 'One orbit.' : count ( $padSiteswapOrbits ) . ' orbits: ' . implode ( '; ', $padSiteswapSaid ) . '.' );

  return padNotationWrap ( padNotationStyle ( 'siteswap', PAD_SITESWAP_COLOURS )
       . padSiteswapSvg ( $padSiteswapSync, $padSiteswapPattern, $padSiteswapOrbit,
                          padNotationId ( 'siteswap', [ $padSiteswapPattern, $padSiteswapTitle ] ), $padSiteswapTitle, $padSiteswapDesc ) );

?>

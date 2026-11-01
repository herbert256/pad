<?php

  // signals - a text spelt in the maritime signal flags of the International Code of Signals:
  // a flag per letter in the code's colours and patterns, a numeral pennant per digit, each
  // with its letter under it and, on hover, its name in the phonetic alphabet and what it
  // means hoisted alone. size= is the width of a flag in pixels (40 unless given), title= the
  // accessible name; the description names every flag.
  //
  //   {signals 'PAD'/}
  //   {signals 'SOS 112', size=30/}

  list ( $padSignalsWords, $padSignalsUnknown ) = padSignalsWords ( $padNotationText );

  if ( $padSignalsUnknown and $padCheckSyntax )
    padError ( "the " . padNotationWhat () . " has no signal flag for '" . padMakeSafe ( implode ( '', $padSignalsUnknown ), 20 )
               . "' - the code has a flag for every letter and a pennant for every digit" );

  if ( ! $padSignalsWords )
    return '';

  $padSignalsSize  = padNotationInt ( 'size', 40, 10, 400 );
  $padSignalsTitle = padNotationOption ( 'title' );

  if ( $padSignalsTitle === '' )
    $padSignalsTitle = padMakeSafe ( $padNotationText, 200 ) . ' in signal flags';

  return padNotationWrap ( padNotationStyle ( 'signals', PAD_SIGNALS_COLOURS )
       . padSignalsSvg ( $padSignalsWords, $padSignalsSize, padNotationId ( 'signals', [ $padNotationText, $padSignalsSize, $padSignalsTitle ] ), $padSignalsTitle ) );

?>

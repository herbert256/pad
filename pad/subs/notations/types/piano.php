<?php

  // piano - notes on a piano keyboard, the keys pressed marked and named: the notes written
  // out - C4 E4 G4, Bb3 D4 F4 (a note without its octave climbs from the one before, from the
  // fourth octave) - or one chord name - Cmaj7, F#m, G7/B - voiced upwards from its root in the
  // fourth octave, its bass an octave lower. The keyboard runs from the C under the lowest note
  // to the B over the highest, two octaves at least; from= and to= set its ends, C3 and B5.
  // size= is its height in pixels (120 unless given), title= the accessible name.
  //
  //   {notation:piano 'C4 E4 G4'/}
  //   {notation:piano 'Dm7'/}
  //   {notation:piano 'A3 C#4 E4', from='C3', to='B4'/}

  $padPianoFound = padPianoNotes ( $padNotationText );

  if ( is_string ( $padPianoFound ) ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has $padPianoFound" );
    return '';
  }

  list ( $padPianoKeys, $padPianoWords ) = $padPianoFound;

  if ( ! $padPianoKeys )
    return '';

  $padPianoLow  = min ( array_keys ( $padPianoKeys ) );
  $padPianoHigh = max ( array_keys ( $padPianoKeys ) );
  $padPianoFrom = max ( 21, $padPianoLow - $padPianoLow % 12 );
  $padPianoTo   = min ( 108, max ( $padPianoHigh + 11 - $padPianoHigh % 12, $padPianoFrom + 23 ) );

  foreach ( [ 'from', 'to' ] as $padPianoEnd ) {
    $padPianoGiven = padNotationOption ( $padPianoEnd );
    if ( $padPianoGiven === '' )
      continue;
    $padPianoMidi = padPianoKey ( $padPianoGiven );
    if ( $padPianoMidi === NULL ) {
      if ( $padCheckSyntax )
        padError ( "the " . padNotationWhat () . " has no key $padPianoEnd='" . padNotationShow ( $padPianoGiven ) . "' - a key is a note and its octave, A0 to C8" );
      continue;
    }
    if ( $padPianoEnd == 'from' )
      $padPianoFrom = $padPianoMidi;
    else
      $padPianoTo = $padPianoMidi;
  }

  if ( $padPianoFrom > $padPianoLow or $padPianoTo < $padPianoHigh ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has notes outside its keys from= to= - the keyboard must hold " . reset ( $padPianoKeys ) . ' to ' . end ( $padPianoKeys ) );
    $padPianoFrom = min ( $padPianoFrom, $padPianoLow );
    $padPianoTo   = max ( $padPianoTo, $padPianoHigh );
  }

  $padPianoSize  = padNotationInt ( 'size', 120, 40, 600 );
  $padPianoTitle = padNotationOption ( 'title' );
  $padPianoList  = implode ( ', ', $padPianoKeys );

  if ( $padPianoTitle === '' )
    $padPianoTitle = ( $padPianoWords !== '' ? "$padPianoWords on a piano: " : 'Piano keys ' ) . $padPianoList;

  return padNotationWrap ( padNotationStyle ( 'piano', PAD_PIANO_COLOURS )
       . padPianoSvg ( $padPianoKeys, $padPianoFrom, $padPianoTo, $padPianoSize,
                       padNotationId ( 'piano', [ $padPianoKeys, $padPianoFrom, $padPianoTo, $padPianoSize, $padPianoTitle ] ),
                       $padPianoTitle, "Keys pressed: $padPianoList." ) );

?>

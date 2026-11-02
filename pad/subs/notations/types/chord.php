<?php

  // chord - a chord of a guitar or a ukulele as the box of a chord chart: its name above, a
  // dot per finger, a bar where one finger lies across the strings, o and × over the strings
  // played open and not played, the note of every string below. The text is a chord name -
  // C, Am, F#m7, Bbmaj7, Dsus4, G7/B, Cdim7 ... - drawn in the shape a player learns first, or
  // a shape found for it when there is none; or the frets themselves, 'x32010', '8 10 10 9 8 8'
  // past the ninth fret, the chord then named by its notes. frets= draws a name in a shape of
  // your own, instrument= is guitar (the default) or ukulele, size= the width in pixels (120
  // unless given), title= the accessible name. Bare, {chart:chord} is the chord diagram of the
  // charts, which comes first: this is {notation:chord}.
  //
  //   {notation:chord 'Am'/}
  //   {notation:chord 'F', instrument='ukulele'/}
  //   {notation:chord 'x32010'/}

  $padChordInstrument = padNotationChoice ( 'instrument', 'guitar', array_keys ( PAD_CHORD_TUNINGS ) );
  $padChordStrings    = count ( PAD_CHORD_TUNINGS [$padChordInstrument] [0] );
  $padChordText       = trim ( $padNotationText );
  $padChordGiven      = padNotationOption ( 'frets' );

  if ( $padChordText === '' and $padChordGiven === '' )
    return '';

  $padChordChord = ( $padChordText !== '' ) ? padMusicChord ( $padChordText ) : NULL;
  $padChordFrets = NULL;

  if ( $padChordGiven !== '' ) {
    $padChordFrets = padChordFrets ( $padChordGiven, $padChordStrings );
    if ( $padChordFrets === NULL and $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has frets '" . padNotationShow ( $padChordGiven ) . "' that are no $padChordInstrument shape - a fret or x for each of its $padChordStrings strings, 'x32010'" );
  } elseif ( ! $padChordChord )
    $padChordFrets = padChordFrets ( $padChordText, $padChordStrings );

  if ( $padChordText !== '' and ! $padChordChord and ( $padChordGiven !== '' or $padChordFrets === NULL ) ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has no chord '" . padNotationShow ( $padChordText ) . "' - a root and a quality - C, Am, F#7, Bbmaj7, Dsus4, G/B - or the frets of its $padChordStrings strings, 'x32010'" );
    return '';
  }

  // Frets given with a name are checked against it: every note they play is the chord's.

  if ( $padChordChord and $padChordFrets ) {
    $padChordTones = array_map ( fn ( $one ) => ( $padChordChord [0] + $one ) % 12, $padChordChord [1] );
    if ( $padChordChord [2] !== NULL )
      $padChordTones [] = $padChordChord [2];
    foreach ( $padChordFrets as $padChordAt => $padChordFret )
      if ( $padChordFret !== NULL and ! in_array ( ( PAD_CHORD_TUNINGS [$padChordInstrument] [0] [$padChordAt] + $padChordFret ) % 12, $padChordTones ) and $padCheckSyntax )
        padError ( "the " . padNotationWhat () . " has frets '" . padNotationShow ( $padChordGiven ) . "' that play a note no " . $padChordChord [3] . " has" );
  }

  if ( ! $padChordFrets )
    $padChordFrets = $padChordChord ? padChordShape ( $padChordChord, $padChordInstrument ) : NULL;

  if ( ! $padChordFrets ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " found no $padChordInstrument shape for " . padNotationShow ( $padChordText ) . " - give it frets=" );
    return '';
  }

  // Frets a hand presses at once lie within five: a shape that spans more is named.

  $padChordPressed = array_filter ( $padChordFrets, fn ( $one ) => $one !== NULL and $one > 0 );

  if ( $padChordPressed and max ( $padChordPressed ) - min ( $padChordPressed ) + 1 > 5 and $padCheckSyntax )
    padError ( "the " . padNotationWhat () . " has frets '" . padNotationShow ( $padChordGiven !== '' ? $padChordGiven : $padChordText ) . "' that span "
               . ( max ( $padChordPressed ) - min ( $padChordPressed ) + 1 ) . " frets - a hand presses at most 5 at once" );

  $padChordChord ??= padChordName ( $padChordFrets, PAD_CHORD_TUNINGS [$padChordInstrument] [0] );

  $padChordName  = $padChordChord ? $padChordChord [3] : '';
  $padChordWords = $padChordChord ? $padChordChord [4] : '';
  $padChordSize  = padNotationInt ( 'size', 120, 40, 600 );
  $padChordTitle = padNotationOption ( 'title' );

  if ( $padChordTitle === '' )
    $padChordTitle = ( $padChordWords !== '' ? $padChordWords : 'Chord ' . padChordWrite ( $padChordFrets ) ) . ", $padChordInstrument";

  return padNotationWrap ( padNotationStyle ( 'chord', PAD_CHORD_COLOURS )
       . padChordSvg ( $padChordFrets, $padChordInstrument, $padChordName, $padChordWords, $padChordChord ? $padChordChord [5] : 'C', $padChordSize,
                       padNotationId ( 'chord', [ $padChordFrets, $padChordName, $padChordInstrument, $padChordSize, $padChordTitle ] ), $padChordTitle ) );

?>

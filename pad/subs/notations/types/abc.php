<?php

  // abc - a tune in ABC notation drawn as a staff: the header lines - X: T: the title, C: the
  // composer, M: the meter, L: the unit note length, Q: the tempo, K: the key - then the music,
  // a line of it a line of staff: the notes C D E F G A B c d e f g a b with their octave marks
  // , and ', their accidentals ^ _ =, their lengths A2 A/2 A3/2, broken rhythm A>B, rests z,
  // chords [CEG], ties, triplets (3abc, bar lines | || |] and repeats |: :| with endings [1 [2,
  // chord names "Am" over the notes. A treble clef, the key and time signatures, stems, beams
  // over the notes written together, ledger lines. width= spreads the lines over that many
  // pixels, title= is the accessible name - the T: title unless given; the description says
  // the tune bar by bar.
  //
  //   {abc}
  //     X:1
  //     T:Speed the Plough
  //     M:4/4
  //     L:1/8
  //     K:G
  //     |:GABc dedB|dedB dedB|c2ec B2dB|c2A2 A2BA:|
  //   {/abc}

  if ( trim ( $padNotationText ) === '' )
    return '';

  $padAbcWidth    = padNotationInt ( 'width', 0, 0, 4000 );
  $padAbcTitle    = padNotationOption ( 'title' );
  $padAbcProblems = [];
  $padAbcSvg      = padAbcSvg ( $padNotationText, padNotationId ( 'abc', [ $padNotationText, $padAbcWidth, $padAbcTitle ] ),
                                $padAbcWidth, $padAbcTitle, $padAbcProblems );

  if ( $padAbcProblems and $padCheckSyntax )
    padError ( "the " . padNotationWhat () . ' ' . $padAbcProblems [0] );

  return ( $padAbcSvg === '' ) ? '' : padNotationWrap ( padNotationStyle ( 'abc', [ 'ink' => [ 'currentColor', 'currentColor' ] ] ) . $padAbcSvg );

?>

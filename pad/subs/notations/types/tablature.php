<?php

  // tablature - a tab for a guitar, a bass, a ukulele or any fretted instrument, written as
  // players write it in plain text and drawn as it is printed: a line a string, the highest on
  // top, the fret to press on the line of its string, notes in one column played together, |
  // a bar line. The signs of the On-line Guitar Archive's guide to tab notation: h hammer-on,
  // p pull-off, b bend, r release, / and \ slides, v or ~ vibrato, t tap, x a muted string,
  // <12> a harmonic, (8) a ghost note; a line of text over or under the strings - chord names,
  // the beats, the note lengths - stands in its columns. tuning= names the tuning, standard
  // unless the names before the lines say another - dropd, dadgad, openg, bass, ukulele ... or
  // the notes from the lowest string, 'D A D G B E'; title= is the accessible name. For a
  // screen reader every note is said bar by bar with its string, its fret and its pitch.
  //
  //   {notation:tablature}
  //   e|-------0-----|
  //   B|-----1---1---|
  //   G|---2-------2-|
  //   D|-------------|
  //   A|-3-----------|
  //   E|-------------|
  //   {/notation:tablature}

  if ( trim ( $padNotationText ) === '' )
    return '';

  if ( strlen ( $padNotationText ) > PAD_TABLATURE_LONGEST ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has a tab of " . strlen ( $padNotationText ) . " bytes - a tab is at most " . PAD_TABLATURE_LONGEST );
    $padNotationText = mb_strcut ( $padNotationText, 0, PAD_TABLATURE_LONGEST, 'UTF-8' );
  }

  list ( $padTablatureParts, $padTablatureProblem ) = padTablatureParse ( $padNotationText );

  if ( $padTablatureProblem !== '' and $padCheckSyntax )
    padError ( "the " . padNotationWhat () . " has $padTablatureProblem" );

  if ( ! $padTablatureParts )
    return '';

  $padTablatureCount  = count ( $padTablatureParts [0] ['strings'] );
  $padTablatureGiven  = padNotationOption ( 'tuning' );
  $padTablatureTuning = padTablatureTuning ( $padTablatureGiven, array_column ( $padTablatureParts [0] ['strings'], 0 ), $padTablatureCount );

  // A tuning written with flats - Eb Ab Db Gb Bb Eb - names the notes with flats.

  padTablatureFlats ( (bool) preg_match ( '/[A-Ga-g](?:b|♭)/u', $padTablatureGiven !== '' ? $padTablatureGiven : implode ( ' ', array_column ( $padTablatureParts [0] ['strings'], 0 ) ) ) );

  if ( is_string ( $padTablatureTuning ) ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " has $padTablatureTuning" );
    $padTablatureTuning = padTablatureTuning ( '', array_column ( $padTablatureParts [0] ['strings'], 0 ), $padTablatureCount );
  }

  // The parts of another number of strings than the first are left out of the drawing.

  $padTablatureParts = array_values ( array_filter ( $padTablatureParts, fn ( $part ) => count ( $part ['strings'] ) == $padTablatureCount ) );

  $padTablatureTitle = padNotationOption ( 'title' );

  if ( $padTablatureTitle === '' )
    $padTablatureTitle = "Tablature, $padTablatureCount " . ( $padTablatureCount == 1 ? 'string' : 'strings' );

  return padNotationWrap ( padNotationStyle ( 'tablature', PAD_TABLATURE_COLOURS )
       . padTablatureSvg ( $padTablatureParts, $padTablatureTuning,
                           padNotationId ( 'tablature', [ $padNotationText, $padTablatureTuning, $padTablatureTitle ] ),
                           $padTablatureTitle, padTablatureDescribe ( $padTablatureParts, $padTablatureTuning ) ) );

?>

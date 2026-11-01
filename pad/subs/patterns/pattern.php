<?php

  // The body of a pattern, whichever way it is written: {pattern 'topography', seed=7/}, or the
  // pattern: type - {pattern:topography seed=7/}. A pattern is never reached bare: its names,
  // dots, grid or waves, are the names of fields and pages. The caller sets $padPatternKind; a
  // pattern is a file of subs/patterns/types/, and the files there are all the patterns there
  // are (padPatternKinds, lib/pattern.php).
  //
  // The options every kind takes - seed, colors, size, width, height, opacity, as, title - and
  // its own, angle= of the stripes, levels= of the topography, are read here, each with
  // padTagParm, so one the kind does not take is left unread and named by the strict check.
  // A value a kind cannot use is an error under the strict check, its default drawn without.
  // The pattern is a value, so the tag is single: content between its tags is an error.

  include_once PAD . 'subs/patterns/_lib/pattern.php';

  if ( $padPatternKind === '' ) {
    if ( $padCheckSyntax )
      padError ( "the pattern has no kind - {pattern 'dots'/}, one of " . implode ( ', ', padPatternKinds () ) );
    return '';
  }

  if ( ! padKind ( 'patterns', $padPatternKind ) ) {
    if ( $padCheckSyntax ) {
      $padPatternLike = padPatternSimilar ( $padPatternKind );
      padError ( "there is no pattern named '" . padMakeSafe ( $padPatternKind, 20 ) . "' - "
               . ( $padPatternLike ? 'did you mean ' . implode ( ', ', $padPatternLike ) . '? ' : '' )
               . 'the patterns are ' . implode ( ', ', padPatternKinds () ) );
    }
    return '';
  }

  // The pattern drawn goes on the xref record, which the reference application shows: the
  // pages that draw each pattern.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'pattern', $padPatternKind );

  if ( trim ( $padContent ) !== '' and $padCheckSyntax )
    padError ( "the pattern draws no content - it is a single tag, {pattern '$padPatternKind'/}" );

  $padContent = '';

  $padPatternGiven = [];

  foreach ( padPatternOptions ( $padPatternKind ) as $padPatternName )
    if ( ( $padPatternValue = padTagParm ( $padPatternName, NULL ) ) !== NULL )
      $padPatternGiven [$padPatternName] = $padPatternValue;

  // The title as the characters it holds (padParmText): a field inside the quoted title=
  // arrives escaped by its sanitize step, and the SVG escapes it again.

  if ( isset ( $padPatternGiven ['title'] ) and is_string ( $padPatternGiven ['title'] ) )
    $padPatternGiven ['title'] = padParmText ( $padPatternGiven ['title'] );

  $padPatternSvg = padPatternMake ( $padPatternKind, $padPatternGiven,
                                    fn ( $message ) => $GLOBALS ['padCheckSyntax'] ? padError ( $message ) : NULL );

  // The pattern is a value: a level loops over the data a data= option named, and would write
  // it once per row - so the level gets its one default occurrence back.

  $padData [$pad] = padDefaultData ();

  return ( $padPatternSvg === '' ) ? FALSE : $padPatternSvg;

?>

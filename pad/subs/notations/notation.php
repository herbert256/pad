<?php

  // The body of a notation, whichever way it is written: {notation 'math'}a/b{/notation}, the
  // notation: type - {notation:math}a/b{/notation:math} - or the bare name, {math}a/b{/math},
  // when nothing else has it. The caller sets $padNotationKind, $padParm to the kind's own
  // first parameter - the second of {notation 'braille', 'Hello'/}, whose first names the
  // kind - and $padNotationGiven when there is one.
  //
  // A notation is a file of subs/notations/types/, and the files there are all the notations
  // there are (padNotationTypes, subs/notations/_lib/notation.php). The file reads its options,
  // draws the text and answers the markup; its functions stand in subs/notations/_lib/<kind>.php,
  // included the first time it runs, its style in subs/notations/assets/<kind>.css, written once
  // per page with the CSP nonce.
  //
  // The text is the kind's first parameter - {math 'a/b'/}, {chess $fen/} - as the characters it
  // holds (padParmText: a field inside it arrives as its value, not its escaped form), or else
  // the content between the tags, taken as it stands before the level walks it - so the braces
  // of AsciiMath, {: x :} and {x | x gt 0}, need no {ignore}; the level is left no content of
  // its own to render. A notation with nothing to draw answers nothing, and its @else@ shows.

  include_once PAD . 'subs/notations/_lib/notation.php';

  if ( ! padNotationType ( $padNotationKind ) ) {
    if ( $padCheckSyntax )
      padError ( "there is no notation named '" . padMakeSafe ( $padNotationKind, 20 ) . "' - " . implode ( ', ', padNotationTypes () ) );
    return '';
  }

  // The notation drawn goes on the xref record, which the reference application shows: the
  // pages that draw each kind.

  if ( $padInfoXref ?? FALSE )
    padInfoXref ( 'notation', $padNotationKind );

  $padNotationSource = $padContent;
  $padContent        = '';

  if ( $padNotationGiven and trim ( $padNotationSource ) !== '' and $padCheckSyntax )
    padError ( "the " . padNotationWhat () . " has its text twice - as its parameter and between its tags" );

  if ( $padNotationGiven and ! is_scalar ( $padParm ) ) {
    if ( $padCheckSyntax )
      padError ( "the " . padNotationWhat () . " was given a list where its text goes" );
    $padParm = '';
  }

  $padNotationText = $padNotationGiven ? padParmText ( (string) $padParm ) : padChartDedent ( $padNotationSource );

  if ( padFileExists ( PAD . "subs/notations/_lib/$padNotationKind.php" ) )
    include_once PAD . "subs/notations/_lib/$padNotationKind.php";

  $padNotationHtml = include PAD . "subs/notations/types/$padNotationKind.php";

  // The markup is a value: a level loops over the data a data= option named, and would write
  // the notation once per row - so the level gets its one default occurrence back.

  $padData [$pad] = padDefaultData ();

  return ( $padNotationHtml === '' or $padNotationHtml === FALSE or $padNotationHtml === NULL ) ? FALSE : $padNotationHtml;

?>

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
  // included the first time it runs, its style in www/assets/notations/<kind>.css, written once
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

  // A byte that is no UTF-8 - a visitor's request value, a page saved in Latin-1 - is the
  // replacement character U+FFFD, which no notation has a sign for: named under the strict
  // check, left out of the drawing without it. The kinds split their text as UTF-8, and a
  // split of a text that is none answered FALSE - a TypeError of count() in braille, a
  // foreach() warning in morse, semaphore and signals.

  $padNotationText = padNotationScrub ( $padNotationText );

  if ( padFileExists ( PAD . "subs/notations/_lib/$padNotationKind.php" ) )
    include_once PAD . "subs/notations/_lib/$padNotationKind.php";

  $padNotationHtml = include PAD . "subs/notations/types/$padNotationKind.php";

  // A text with nothing in it draws nothing, whatever its options say: a page that draws a
  // formula or a position of every row - {rows}{morse $t, plain}@else@-{/morse}{/rows} - shows
  // the @else@ for a row whose text is empty. The kind answers nothing before it reads the
  // options that shape a drawing, so they are marked read here, where the strict sweep named
  // each one "an option that nothing reads" and the page ended. A kind that draws without a
  // text - a chord from frets= - answers its drawing and is swept as ever.

  if ( trim ( $padNotationText ) === '' and ( $padNotationHtml === '' or $padNotationHtml === FALSE or $padNotationHtml === NULL ) )
    foreach ( $padParms [$pad] as $padNotationOne )
      if ( $padNotationOne ['padPrmKind'] == 'option' )
        padDone ( $padNotationOne ['padPrmName'] );

  // The markup is a value: a level loops over the data a data= option named, and would write
  // the notation once per row - so the level gets its one default occurrence back.

  $padData [$pad] = padDefaultData ();

  return ( $padNotationHtml === '' or $padNotationHtml === FALSE or $padNotationHtml === NULL ) ? FALSE : $padNotationHtml;

?>

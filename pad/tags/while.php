<?php

  // The {while} and {until} tags: run the level's content again and again for as long as the
  // condition holds ({while}) or does not hold ({until}). tags/until.php is only an include
  // of this file, and $padTag [$pad] is what decides which way round the test goes.
  //
  // The condition is taken raw from the first parameter (padFirstParm) and re-evaluated on
  // every pass. Returning [ 1 => [] ] hands the level a single occurrence to render and
  // $padWalk [$pad] = 'next' asks level/end.php to call this handler once more afterwards;
  // returning NULL with an empty $padWalk ends the loop. When the condition sits on the
  // closing tag instead, padStartAndClose lets the first pass through untested, which turns
  // {while}...{/while $i lt 10} into a do-while.

  // A loop without its closing tag used to fall through as a single tag. Strict mode says
  // what is missing.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  // A handling option - first=, rows=, last=, where=, sort and the rest of handling/types/ -
  // works on rows, and a {while} or {until} has none: it was taken on the first round's one
  // stand-in row and did nothing, and the unread-option sweep was silent, as handling had read
  // it - {while $i lt 5, first=2} ran its five rounds. Strict mode names it.

  if ( $padCheckSyntax and $padWalk [$pad] != 'next' )
    foreach ( $padParms [$pad] as $padWhileOpt )
      if ( ( $padWhileOpt ['padPrmKind'] ?? '' ) == 'option'
           and padFileExists ( PAD . 'handling/types/' . $padWhileOpt ['padPrmName'] . '.php' ) )
        padError ( "the option " . $padWhileOpt ['padPrmName'] . " does nothing on "
                   . ( $padTag [$pad] == 'until' ? 'an' : 'a' ) . " {" . $padTag [$pad] . "} - it has no rows to handle" );

  // A condition that never turns ran forever, and the request died on PHP's clock with
  // nothing said. The loop stops at the ceiling the sequences already use; strict mode
  // says which loop it was.

  if ( $padWalk [$pad] == 'next' )
    $padWhileRound [$pad] = ( $padWhileRound [$pad] ?? 1 ) + 1;
  else
    $padWhileRound [$pad] = 1;

  if ( $padWhileRound [$pad] > $padSeqDefaultTries ) {

    if ( $padCheckSyntax )
      padError ( "the {" . $padTag [$pad] . "} passed $padSeqDefaultTries rounds and was stopped" );

    $padWalk [$pad] = '';
    return NULL;

  }

  if ( padStartAndClose ('next') )
    return TRUE;

  // Reading a missing condition was a PHP error; now a conditionless loop simply does
  // not run - and strict mode says why.

  $padWhileOrg = padFirstParm ();

  if ( trim ( $padWhileOrg ) == '' ) {

    if ( $padCheckSyntax )
      padError ( "the {" . $padTag [$pad] . "} has no condition" );

    $padWalk [$pad] = '';
    return NULL;

  }

  $padWhile = padEvalBool ( $padWhileOrg );

  $padWhileHolds  = ( $padTag [$pad] == 'while' ) ? $padWhile : ! $padWhile;
  $padWalk [$pad] = $padWhileHolds ? 'next' : '';

  if ( $padWhileHolds )
    return [ 1 => [] ];

  // A loop that never ran a round is a tag that came back empty and shows its @else@; it
  // answered nothing, and the branch never showed. One that ran its rounds just ends.

  return ( $padWhileRound [$pad] == 1 ) ? FALSE : NULL;

?>

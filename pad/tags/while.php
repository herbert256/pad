<?php

  // The {while} and {until} tags: run the level's content again and again for as long as the
  // condition holds ({while}) or does not hold ({until}). tags/until.php is only an include
  // of this file, and $padTag [$pad] is what decides which way round the test goes.
  //
  // The condition is taken raw from $padParms [$pad] [0] ['padPrmOrg'] and re-evaluated on
  // every pass. Returning [ 1 => [] ] hands the level a single occurrence to render and
  // $padWalk [$pad] = 'next' asks level/end.php to call this handler once more afterwards;
  // returning NULL with an empty $padWalk ends the loop. When the condition sits on the
  // closing tag instead, padStartAndClose lets the first pass through untested, which turns
  // {while}...{/while $i lt 10} into a do-while.

  // A loop without its closing tag used to fall through as a single tag. Strict mode says
  // what is missing.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    return padError ( "the pair {" . $padOrg [$pad] . "} never closes" );

  // A condition that never turns ran forever, and the request died on PHP's clock with
  // nothing said. The loop stops at the ceiling the sequences already use; strict mode
  // says which loop it was.

  if ( $padWalk [$pad] == 'next' )
    $padWhileRound [$pad] = ( $padWhileRound [$pad] ?? 1 ) + 1;
  else
    $padWhileRound [$pad] = 1;

  if ( $padWhileRound [$pad] > $padSeqDefaultTries ) {

    if ( $padCheckSyntax )
      return padError ( "the {" . $padTag [$pad] . "} passed $padSeqDefaultTries rounds and was stopped" );

    $padWalk [$pad] = '';
    return NULL;

  }

  if ( padStartAndClose ('next') )
    return TRUE;

  // Reading a missing condition was a PHP error; now a conditionless loop simply does
  // not run - and strict mode says why.

  $padWhileOrg = $padParms [$pad] [0] ['padPrmOrg'] ?? '';

  if ( trim ( $padWhileOrg ) == '' ) {

    if ( $padCheckSyntax )
      return padError ( "the {" . $padTag [$pad] . "} has no condition" );

    $padWalk [$pad] = '';
    return NULL;

  }

  $padWhile = padEvalBool ( $padWhileOrg );

  if ($padTag [$pad] == 'while') {

    $padWalk [$pad] = (   $padWhile ) ? 'next' : '';
    return            (   $padWhile ) ? [ 1 => [] ] : NULL;

  } else {

    $padWalk [$pad] = ( ! $padWhile ) ? 'next' : '';
    return            ( ! $padWhile ) ? [ 1 => [] ] : NULL;

  }

?>
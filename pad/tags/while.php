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

  if ( padStartAndClose ('next') )
    return TRUE;

  $padWhile = padEvalBool ( $padParms [$pad] [0] ['padPrmOrg'] );

  if ($padTag [$pad] == 'while') {

    $padWalk [$pad] = (   $padWhile ) ? 'next' : '';
    return            (   $padWhile ) ? [ 1 => [] ] : NULL;

  } else {

    $padWalk [$pad] = ( ! $padWhile ) ? 'next' : '';
    return            ( ! $padWhile ) ? [ 1 => [] ] : NULL;

  }

?>
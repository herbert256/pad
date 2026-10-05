<?php

  // Opens a new occurrence: bumps the row counter, gives the level a fresh working copy
  // of its template ($padOut = $padBase, its comments taken out and its ~ whitespace
  // control applied) and points
  // $padKey/$padCurrent at the row the data pointer currently sits on.
  //
  // Rows reached after the first walk pass are also collected in $padWalkData, which is
  // what the toData= option ends up storing.

  $padOccur [$pad]++;

  $padParm = $padOpt [$pad] [1] ?? '';

  $padOccurStart [$pad] [$padOccur[$pad]] = TRUE;

  $padOut     [$pad] = padTildeStrip ( padCommentStrip ( $padBase [$pad] ) );
  $padScan    [$pad] = 0;
  $padKey     [$pad] = key($padData [$pad]);
  $padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];

  if ( $padWalk [$pad] != 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

?>
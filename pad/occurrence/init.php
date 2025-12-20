<?php

  $padOccur [$pad]++;

  $padParm = $padOpt [$pad] [1] ?? '';

  $padOccurStart [$pad] [$padOccur[$pad]] = TRUE;

  $padOut     [$pad] = $padBase [$pad];
  $padKey     [$pad] = key($padData [$pad]);
  $padCurrent [$pad] = $padData [$pad] [$padKey [$pad]];

  if ( $padWalk [$pad] <> 'start' )
    $padWalkData [$pad] [] = $padCurrent [$pad];

?>

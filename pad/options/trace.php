<?php
  
  $padWalkSave [$pad] = $padWalk [$pad];
  $padPairSave [$pad] = $padPair [$pad];

  $padPair [$pad] = TRUE;
  $padWalk [$pad] = 'start';

  include PAD . 'pad/tags/trace.php';
  
  $padWalk [$pad] = $padWalkSave [$pad];
  $padPair [$pad] = $padPairSave [$pad];

  $padEndOptions [$pad] [] = 'trace';

?>
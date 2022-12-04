<?php
  
  $padWalkSave [$pad] = $padWalk [$pad];
  $padPairSave [$pad] = $padPair [$pad];

  $padPair [$pad] = TRUE;
  $padWalk [$pad] = 'end';

  include PAD . 'tags/trace.php';
  
  $padWalk [$pad] = $padWalkSave [$pad];
  $padPair [$pad] = $padPairSave [$pad];

?>
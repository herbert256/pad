<?php
  
  $padWalkSave [$pad] = $padWalk [$pad];
  $padPairSave [$pad] = $padPair [$pad];

  $padPair [$pad] = TRUE;
  $padWalk [$pad] = 'end';

  include pad . 'tags/trace.php';
  
  $padWalk [$pad] = $padWalkSave [$pad];
  $padPair [$pad] = $padPairSave [$pad];

?>
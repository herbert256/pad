<?php

  $padBase [$pad] = $padEndBase [$pad];
  $padData [$pad] = padDefaultData ();

  $padEndBase [$pad] = '';

  reset ( $padData [$pad] );

  $padInfoOccur = 'end2'; 
  include '/pad/occurrence/start.php';

?>
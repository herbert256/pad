<?php

  $padBase [$pad] = $padEndBase [$pad];
  $padData [$pad] = padDefaultData ();

  $padEndBase [$pad] = '';

  reset ( $padData [$pad] );

  $padInfoOccur = 'end2'; 
  include 'occurrence/start.php';

?>
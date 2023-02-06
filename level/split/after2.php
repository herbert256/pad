<?php

  $padBase  [$pad] = $padAfter [$pad];
  $padData  [$pad] = padDefaultData ();

  $padAfter [$pad] = '';

  reset ( $padData [$pad] );
  include PAD . 'occurrence/start.php';

?>
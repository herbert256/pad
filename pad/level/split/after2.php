<?php

  $padBase  [$pad] = $padAfter [$pad];
  $padData  [$pad] = padDefaultData ();

  $padAfter [$pad] = '';

  $padOccur [$pad] = 998;

  reset ( $padData [$pad] );
  include pad . 'occurrence/start.php';

?>
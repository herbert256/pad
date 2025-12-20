<?php

  $padBase [$pad] = $padEndBase [$pad];
  $padData [$pad] = padDefaultData ();

  $padEndBase [$pad] = '';

  reset ( $padData [$pad] );

  include PAD . 'occurrence/occurrence.php';

?>

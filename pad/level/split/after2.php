<?php

  $padBase [$pad] = $padAfterBase [$pad];
  $padData [$pad] = padDefaultData ();

  $padAfterBase [$pad] = '';

  reset ( $padData [$pad] );
  include pad . 'occurrence/start.php';

?>
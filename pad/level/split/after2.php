<?php

  $padBase [$pad] = $padAfterBase [$pad];
  $padData [$pad] = padDefaultData ();

  $padAfterBase [$pad] = '';

  reset ( $padData [$pad] );

  $padOccurTypeSet = 'after';  
  include pad . 'occurrence/start.php';

?>
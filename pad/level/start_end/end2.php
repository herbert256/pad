<?php

  $padBase [$pad] = $padEndBase [$pad];
  $padData [$pad] = padDefaultData ();

  $padEndBase [$pad] = '';

  reset ( $padData [$pad] );

  $padOccurTypeSet = 'after';  
  include pad . 'occurrence/start.php';

?>
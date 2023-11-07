<?php

  $padBase [$pad] = $padBeforeBase [$pad];
  $padData [$pad] = $padBeforeData [$pad];

  $padBeforeBase [$pad] = '';
  $padBeforeData [$pad] = [];

  reset ( $padData [$pad] );
  
  $padOccurTypeSet = 'between';  
  include pad . 'occurrence/start.php';

?>
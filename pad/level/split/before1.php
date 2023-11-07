<?php

  list ( $padBase [$pad], $padBeforeBase [$pad] ) = explode ( '@start@', $padBase [$pad], 2 );

  $padBeforeData [$pad] = $padData [$pad]; 
  $padData [$pad]       = padDefaultData ();

  reset ( $padData [$pad] );

  $padOccurTypeSet = 'before';  
  include pad . 'occurrence/start.php';
   
?>
<?php

  if ( $GLOBALS ['padInfo'] ) 
    include PAD . 'events/start.php';
  
  list ( $padBase [$pad], $padStartBase [$pad] ) = explode ( '@start@', $padBase [$pad], 2 );

  $padStartData [$pad] = $padData [$pad]; 
  $padData [$pad]      = padDefaultData ();

  reset ( $padData [$pad] );

  $padInfoOccur = 'start1'; 
  include PAD . 'occurrence/start.php';
   
?>
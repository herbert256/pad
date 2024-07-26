<?php

  if ( padXref ) include pad . 'info/types/xref/events/start.php';
  if ( padXapp ) include pad . 'info/types/xapp/events/start.php';
  
  list ( $padBase [$pad], $padStartBase [$pad] ) = explode ( '@start@', $padBase [$pad], 2 );

  $padStartData [$pad] = $padData [$pad]; 
  $padData [$pad]      = padDefaultData ();

  reset ( $padData [$pad] );

  include pad . 'occurrence/start.php';
   
?>
<?php

  if ( padXref ) 
    include pad . 'tail/types/xref/items/start.php';
  
  list ( $padBase [$pad], $padStartBase [$pad] ) = explode ( '@start@', $padBase [$pad], 2 );

  $padStartData [$pad] = $padData [$pad]; 
  $padData [$pad]      = padDefaultData ();

  reset ( $padData [$pad] );

  $padOccurTypeSet = 'before';  
  include pad . 'occurrence/start.php';
   
?>
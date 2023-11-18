<?php

  $padStartType = 'pad';

  if ( $padXref ) 
    padXref ( 'start', $padStartType );

  include pad . 'start/lib/setup.php';

  $padBase [$pad] = $padCode;    

  $padOccurTypeSet = 'code';  
  include pad . 'occurrence/start.php'; 

  include pad . 'start/lib/level.php'; 

  $padCode = $padPad [$pad+1];

?>
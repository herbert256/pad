<?php

  $padStartType = 'pad';

  if ( $padXref ) 
    include pad . 'xref/items/entry.php';

  include pad . 'start/lib/setup.php';

  $padBase [$pad] = $padCode;    

  $padOccurTypeSet = 'code';  
  include pad . 'occurrence/start.php'; 

  include pad . 'start/lib/level.php'; 

  $padCode = $padPad [$pad+1];

?>
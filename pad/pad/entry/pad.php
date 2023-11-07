<?php

  include pad . 'pad/lib/setup.php';

  $padBase [$pad] = $padCode;    

  $padOccurTypeSet = 'pad';  
  include pad . 'occurrence/start.php'; 

  include pad . 'pad/lib/level.php'; 

  $padCode = $padPad [$pad+1];

?>
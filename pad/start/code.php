<?php

  include pad . 'inits/level.php';

  $padBase [$pad] = $padCode;    

  include pad . 'occurrence/start.php'; 
  include pad . 'start/lib/level.php'; 

  $padCode = $padPad [$pad+1];

?>
<?php

  include_once pad . 'inits/levelVars.php';

  $padFunSave = padSave ();

  foreach ( $GLOBALS as $padLoopK => $padLoopV )
    global $$padLoopK;

  include pad . 'inits/level.php';

  $padBase [$pad] = $padFun;    

  include pad . 'occurrence/start.php'; 
  include pad . 'start/_lib/level.php'; 

  padRestore ( $padFunSave );

  return $padPad [$pad+1];

?>
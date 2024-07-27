<?php

  include_once pad . 'inits/levelVars.php';

  foreach ( $GLOBALS as $padLoopK => $padLoopV )
    global $$padLoopK;

  include pad . 'start/code/solitary/backup.php';  
  include pad . 'start/code/solitary/reset.php';  
  include pad . 'inits/level.php'; 
  
  foreach ( $padSetLvl [$pad-1] as $k => $v ) {
    $GLOBALS [$k] = $v;
    global $$k;
  }

  $padBase [$pad] = $padSol;    

  include pad . 'occurrence/start.php'; 
  
  include pad . 'start/_lib/level.php'; 

  include pad . 'start/solitary/restore.php';  

  return $padPad [$pad+1];

?>
<?php

  include_once pad . 'inits/levelVars.php';

  foreach ( $GLOBALS as $padLoopK => $padLoopV )
    global $$padLoopK;

  include pad . 'pad/code/solitary/backup.php';  
  include pad . 'pad/code/solitary/reset.php';  
  include pad . 'inits/level.php'; 
  
  foreach ( $padSetLvl [$pad-1] as $k => $v ) {
    $GLOBALS [$k] = $v;
    global $$k;
  }

  $padBase [$pad] = $padSol;    

  include pad . 'occurrence/start.php'; 
  
  include pad . 'pad/_lib/level.php'; 

  include pad . 'pad/solitary/restore.php';  

  return $padPad [$pad+1];

?>
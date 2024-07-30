<?php

  global $padSolSave, $padSolApp, $padSolData, $padSolCnt;

  if ( $padMethod == 'function' )
    foreach ( $GLOBALS as $padLoopK => $padLoopV )
      global $$padLoopK;

  if ( ! isset ( $padSolCnt ) ) 
    $padSolCnt = 0;
  else
    $padSolCnt++;

  include pad . 'start/_lib/backup.php';
  include pad . 'start/_lib/reset.php';

  if ( $padMethod == 'function' )
    foreach ( $padSetLvl [$pad-1] as $k => $v ) {
      $GLOBALS [$k] = $v;
      global $$k;
    }

  include pad . 'inits/level.php'; 
  
  $padBase [$pad] = $padCode;    

  include pad . 'occurrence/start.php'; 
  include pad . 'start/_lib/level.php'; 
  include pad . 'start/_lib/restore.php';

  $padSolCnt--;
  
  return $padPad [$pad+1];

?>
<?php

  include pad . 'inits/levelVars.php';

  foreach ( $GLOBALS as $padFunK => $padFunV ) 
    global $$padFunK;

  if ( ! isset ($padFunCnt) )
    $padFunCnt = 0;
  else
    $padFunCnt++;

  foreach ( $GLOBALS as $padFunK => $padFunV ) 
    if ( str_starts_with($padFunK, 'pad') )
      if ( ! str_starts_with($padFunK, 'padFun') )
        if ( ! in_array($padFunK, $padLevelVars ) )
          $padFunSave [$padFunCnt] [$padFunK] = $padFunV; 

  include pad . 'inits/level.php';

  $padBase [$pad] = $padFun;    

  include pad . 'occurrence/start.php'; 
  include pad . 'start/lib/level.php'; 

  foreach ( $padFunSave [$padFunCnt] as $padFunK => $padFunV ) 
    $GLOBALS [$padFunK] = $padFunV;
 
  $padFunCnt--;

  return $padPad [$pad+1];

?>
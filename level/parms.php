<?php

  $padPrmTmp = padParseOptions ( $padOpt [$pad] [0] );

  include 'set.php';
  include 'prm.php';
  
  $padPrmCnt = 0;

  foreach ( $padPrmTmp as $padV ) { 
    $padPrmCnt++; 
    $padOpt [$pad] [$padPrmCnt] = padEval ( $padV );
  }

?>
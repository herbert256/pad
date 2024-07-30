<?php

  for ( $i = 0; $i <= $pad ; $i++ ) {

    $padData    [$i] = [];
    $padCurrent [$i] = [];
    $padSetLvl  [$i] = [];
    $padSetOcc  [$i] = [];
    $padTable   [$i] = [];
    $padPrm     [$i] = [];
    $padOpt     [$i] = [];

  }

  $padTables = $padRelations = $padDataStore = $padContentStore = $padFlagStore = $padSeqStore = [];
   
  if ( isset ( $padSqlConnect ) )
    unset ( $padSqlConnect );
  
?>
<?php

  include_once pad . 'inits/levelVars.php';
  
  $padPageApp [$pad] = [];
  $padPagePad [$pad] = padSave ();

  foreach ( $GLOBALS as $padLoopK => $padLoopV )
    if ( padValidStore ($padLoopK) ) {
      $padPageApp [$pad] [$padLoopK] = $padLoopV;
      unset ( $GLOBALS [$padLoopK] );
    }

  foreach ( $padSetLvl [$pad] as $padK => $padV )
     $GLOBALS [$padK] = $padV;

  $padTables = $padRelations = $padDataStore = $padContentStore = $padFlagStore = $padSeqStore = [];
   
  if ( isset ( $padSqlConnect ) )
    unset ( $padSqlConnect );

?>
<?php

  $GLOBALS ['padBanaanGlobal'] [$GLOBALS['pad']] = [];
  $GLOBALS ['padBanaanPAD']    [$GLOBALS['pad']] = [];
  $GLOBALS ['padBanaanSEQ']    [$GLOBALS['pad']] = [];

  foreach ( $GLOBALS as $padK => $padV) 
    if ( substr($padK, 0, 6) == 'padSeq' ) {
      $padBanaanSEQ [$pad] [$padK] = $padV;
      unset ( $GLOBALS [$padK] );
    }

  foreach ( $GLOBALS as $padK => $padV )
    if ( substr($padK, 0, 3) == 'pad' and $padK <> 'padK' ) 
      global $$padK;
 
  foreach ( $GLOBALS as $padK => $padV )
    if ( substr($padK, 0, 3) == 'pad' and $padK <> 'padK' and $padK <> 'padV' )
      if ( ! in_array($padK, $padLevelVars) ) 
      $padBanaanPAD [$pad] [$padK] = $padV;

  foreach ( $GLOBALS as $padK => $padV )
    if ( padValidStore ($padK) ) {
      $padBanaanGlobal [$pad] [$padK] = $padV;
      unset ( $GLOBALS [$padK] );
    }

   foreach ( $padSetLvl [$pad] as $padK => $padV  ) { 
     global $$padK;
     $$padK = $padV;
   }

  $padTablesInsert       [$pad] = $padTables       ?? [];
  $padRelationsInsert    [$pad] = $padRelations    ?? [];
  $padDataStoreInsert    [$pad] = $padDataStore    ?? [];
  $padContentStoreInsert [$pad] = $padContentStore ?? [];
  $padFlagStoreInsert    [$pad] = $padFlagStore    ?? [];
  
  $padTables = $padRelations = $padDataStore = $padContentStore = $padFlagStore = [];
   
  if ( isset ( $padSqlConnect) ) {
    $padSqlConnectInsert [$pad] = $padSqlConnect;
    unset ($padSqlConnect);
  }

  include pad . 'page/start.php';
  include pad . 'page/setup.php';
  
  array_push ( $GLOBALS ['padBanaan'], $padPage );

  include pad . 'build/build.php';
  include pad . 'page/level.php';   

  array_pop  ( $GLOBALS ['padBanaan'] );

  include pad . 'page/end.php';

  if ( isset ( $padSqlConnectInsert [$pad]) ) {
    $padSqlConnect = $padSqlConnectInsert [$pad];
    unset ($padSqlConnectInsert [$pad]);
  }

  $padTables       = $padTablesInsert       [$pad];
  $padRelations    = $padRelationsInsert    [$pad];

  $padDataStore    = $padDataStoreInsert    [$pad];
  $padContentStore = $padContentStoreInsert [$pad];
  $padFlagStore    = $padFlagStoreInsert    [$pad];

  foreach ( $padBanaanGlobal [$pad] as $padK => $padV  ) { 
     unset ( $GLOBALS [$padK] );
     $GLOBALS [$padK] = $padV;
  }

  foreach ($GLOBALS as $padK => $padV )
    if ( padValidStore ($padK) and ! isset ( $padBanaanGlobal [$pad] [$padK] ) ) 
       unset ( $GLOBALS [$padK] ); 

  foreach ( $padBanaanPAD [$pad] as $padK => $padV  ) { 
     unset ( $GLOBALS [$padK] );
     $GLOBALS [$padK] = $padV;
  }

  foreach ($GLOBALS as $padK => $padV )
    if ( substr($padK, 0, 3) == 'pad' and $padK <> 'padK' )
      if  ( ! in_array($padK, $padLevelVars) and ! isset ( $padBanaanPAD [$pad] [$padK] ) ) 
        unset ( $GLOBALS [$padK] ); 

  foreach ( $GLOBALS as $padK => $padV) 
    if ( substr($padK, 0, 6) == 'padSeq' )
      unset ( $GLOBALS [$padK] );
 
  foreach ( $padBanaanSEQ [$pad] as $padK => $padV) 
      $GLOBALS [$padK] = $padPageSeq [$pad] [$padK];

  return $padHtml [$pad+1];

?>
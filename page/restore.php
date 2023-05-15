<?php
 
  foreach ( $GLOBALS as $padK => $padV )
    if ( padValidStore ($padK) )
      unset ( $GLOBALS [$padK] );

  foreach ( $padInsertInsert [$pad] as $padK => $padV )
    $GLOBALS [$padK] = $padInsertInsert [$pad] [$padK];
    
  $padTables       = $padTablesInsert       [$pad];
  $padRelations    = $padRelationsInsert    [$pad];

  $padDataStore    = $padDataStoreInsert    [$pad];
  $padContentStore = $padContentStoreInsert [$pad];
  $padFlagStore    = $padFlagStoreInsert    [$pad];

  if ( isset ( $padSqlConnectInsert [$pad]) ) {
    $padSqlConnect = $padSqlConnectInsert [$pad];
    unset ($padSqlConnectInsert [$pad]);
  }

?>
<?php
 
  $padInsertInsert [$pad] = [];    

  foreach ($GLOBALS as $padK => $padV)
    if ( padValidStore ($padK) ) {
      $padInsertInsert [$pad] [$padK] = $GLOBALS [$padK];
      unset ( $GLOBALS [$padK] );
    }

  $padTablesInsert       [$pad] = $padTables       ?? [];
  $padRelationsInsert    [$pad] = $padRelations    ?? [];
  
  $padDataStoreInsert    [$pad] = $padDataStore    ?? [];
  $padContentStoreInsert [$pad] = $padContentStore ?? [];
  $padFlagStoreInsert    [$pad] = $padFlagStore    ?? [];
   
  if ( isset ( $padSqlConnect) )
    $padSqlConnectInsert [$pad] = $padSqlConnect;
   
  include 'reset.php';

?>
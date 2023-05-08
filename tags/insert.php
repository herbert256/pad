<?php
 
  $padInsertInsert [$pad] = [];    
  foreach ($GLOBALS as $padK => $padV)
    if ( padValidStore ($padK) ) {
      $padInsertInsert [$pad] [$padK] = $GLOBALS [$padK];
      unset ( $GLOBALS [$padK] );
    }

  $padPageInsert [$pad]       = $padPage;
  $padIncludeInsert [$pad]    = $padInclude;
  $padBuildModeInsert [$pad]  = $padBuildMode;
  $padBuildMergeInsert [$pad] = $padBuildMerge;  
  $padTablesInsert [$pad]     = $padTables    ?? [];
  $padRelationsInsert [$pad]  = $padRelations ?? [];
  
  $padDataStoreInsert [$pad]    = $padDataStore    ?? [];
  $padContentStoreInsert [$pad] = $padContentStore ?? [];
  $padFlagStoreInsert [$pad]    = $padFlagStore    ?? [];
  $padSeqStoreInsert [$pad]     = $padSeqStore     ?? [];
  
  $padTables = $padRelations = $padDataStore = $padContentStore = $padFlagStore = $padSeqStore = [];

  if ( isset ( $padSqlConnect) ) {
    $padSqlConnectInsert [$pad] = $padSqlConnect;
    unset ($padSqlConnect);
  }
      
  $padIncludeSet     = padTagParm ( 'include', $padInclude        );
  $padBuildModeSet   = padTagParm ( 'mode',    $padBuildMode      );
  $padBuildMergeSet  = padTagParm ( 'merge',   $padBuildMerge     );
  $padPage           = padTagParm ( 'page',    $padOpt [$pad] [1] );

  $padInsertLevel = $pad;

  $padBetween   = 'true';
  $padFirst     = 't';
  $padWords     = [];
  $padWords [0] = 'true';

  $padTypeCheck  = 'true';
  $padTypeResult = 'pad';
  $padTypeGiven  = FALSE;
  $padPairSet    = FALSE;
  $padTrueSet    = '';
  $padPrmTypeSet = 'none';

  include pad . 'level/setup.php'; 
  include pad . 'build/build.php'; 

  unset ( $padIncludeSet    );
  unset ( $padBuildModeSet  );
  unset ( $padBuildMergeSet ); 

  $padData    [$pad] = padDefaultData ();
  $padKey     [$pad] = 999;
  $padCurrent [$pad] = $padData [$pad] [999];

  while ( $pad > $padInsertLevel ) 
    include pad . 'level/level.php'; 

  if ( isset ( $padSqlConnectInsert [$pad]) ) {
    $padSqlConnect = $padSqlConnectInsert [$pad];
    unset ($padSqlConnectInsert [$pad]);
  }

  $padTables    = $padTablesInsert [$pad];
  $padRelations = $padRelationsInsert [$pad];
  $padSeqStore  = $padSeqStoreInsert [$pad];

  $padPage       = $padPageInsert [$pad];
  $padInclude    = $padIncludeInsert [$pad];
  $padBuildMode  = $padBuildModeInsert [$pad];
  $padBuildMerge = $padBuildMergeInsert [$pad];  

  foreach ($GLOBALS as $padK => $padV)
    if ( padValidStore ($padK) )
      unset ( $GLOBALS[$padK] );

  foreach ( $padInsertInsert [$pad] as $padK => $padV )
    $GLOBALS [$padK] = $padInsertInsert [$pad] [$padK];

  return $padHtml [$pad+1];

?>
<?php

  if ( $padWalk [$pad] == 'start' ) {  

    $padWalk [$pad] = 'end';

    $padBackupTrace [$pad] = [];
    $padBackupTrace [$pad] ['trace'] = $padTrace;
    $padBackupTrace [$pad] ['level'] = $padLevelDir [$pad-1] ?? '';
    $padBackupTrace [$pad] ['occur'] = $padOccurDir [$pad-1] ?? '';

    $padLevelDir [$pad-1] = $padTraceDir . '/trace-' . $padCnt; 
    $padOccurDir [$pad-1] = $padLevelDir [$pad-1];

    pTraceAll ( $padLevelDir [$pad] . "/start" );
  
  } else {

    pTraceAll ( $padLevelDir [$pad] . "/end" );

    $padTrace           = $padBackupTrace [$pad] ['trace'];
    $padLevelDir [$pad-1] = $padBackupTrace [$pad] ['level'];
    $padOccurDir [$pad-1] = $padBackupTrace [$pad] ['occur'];
 
  } 

  return TRUE;
     
?>
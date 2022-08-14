<?php

  if ( $pWalk [$p] == 'start' ) {  

    $pWalk [$p] = 'end';

    $pBackupTrace [$p] = [];
    $pBackupTrace [$p] ['trace'] = $pTrace;
    $pBackupTrace [$p] ['level'] = $pLevelDir [$p-1] ?? '';
    $pBackupTrace [$p] ['occur'] = $pOccurDir [$p-1] ?? '';

    $pLevelDir [$p-1] = $pTraceDir . '/trace-' . $pCnt; 
    $pOccurDir [$p-1] = $pLevelDir [$p-1];

    pTraceAll ( $pLevelDir [$p] . "/start" );
  
  } else {

    pTraceAll ( $pLevelDir [$p] . "/end" );

    $pTrace           = $pBackupTrace [$p] ['trace'];
    $pLevelDir [$p-1] = $pBackupTrace [$p] ['level'];
    $pOccurDir [$p-1] = $pBackupTrace [$p] ['occur'];
 
  } 

  return TRUE;
     
?>
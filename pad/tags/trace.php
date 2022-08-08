<?php

  if ( $pWalk [$p] == 'start' ) {  

    $pWalk [$p] = 'end';

    $pBackupTrace [$p] = [];
    $pBackupTrace [$p] ['trace'] = $pTrace;
    $pBackupTrace [$p] ['level'] = $pLevelDir [$p-1] ?? '';
    $pBackupTrace [$p] ['occur'] = $pOccurDir [$p-1] ?? '';

    $pLevelDir [$p-1] = $pTraceDir . '/trace-' . $pCnt; 
    $pOccurDir [$p-1] = $pLevelDir [$p-1];

    pFile_put_contents ( $pLevelDir [$p] . "/pad-start.json", pTrace_get_pVars ()  );
    pFile_put_contents ( $pLevelDir [$p] . "/app-start.json", pTrace_get_app_vars ()  );
  
  } else {

    pFile_put_contents ( $pLevelDir [$p] . "/pad-end.json", pTrace_get_pVars ()  );
    pFile_put_contents ( $pLevelDir [$p] . "/app-end.json", pTrace_get_app_vars ()  );

    $pTrace           = $pBackupTrace [$p] ['trace'];
    $pLevelDir [$p-1] = $pBackupTrace [$p] ['level'];
    $pOccurDir [$p-1] = $pBackupTrace [$p] ['occur'];
 
  } 

  return TRUE;
     
?>
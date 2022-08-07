<?php

  if ( $pWalk[$p] == 'start' ) {  

    $pWalk[$p] = 'end';

    $pBackup_trace = [];

    foreach ($GLOBALS as $pK2 => $pV2 )
      if ( substr($pK2, 0, 9) == 'pTrace' )
        $pBackup_trace [$pK2] = $pV2;

    $pTrace           = TRUE;  
    $pTrace     = TRUE;  
    $pTrace = TRUE;  
    $pTrace    = TRUE;  
    $pTrace      = TRUE;  
    $pTrace_type = TRUE;  
    $pTrace      = TRUE;  
    $pTrace     = TRUE;  
    $pTrace   = TRUE;  
    $pTrace   = TRUE;  
    $pTrace   = TRUE;  
    $pTrace       = TRUE; 
    $pTrace   = TRUE;
    $pTrace       = TRUE; 

    $pLevelDir[$p] = $pTraceDir . '/trace-' . $pCnt; 
    $pOccurDir[$p] = $pLevelDir;

    $pLevelDir[$p]= $pLevelDir;
    $pOccurDir[$p]= $pOccurDir;

    pFile_put_contents ( $pLevelDir[$p] . "/pad-start.json", pTrace_get_pVars ()  );
    pFile_put_contents ( $pLevelDir[$p] . "/app-start.json", pTrace_get_app_vars ()  );
  
  } else {

    pFile_put_contents ( $pLevelDir[$p] . "/pad-end.json", pTrace_get_pVars ()  );
    pFile_put_contents ( $pLevelDir[$p] . "/app-end.json", pTrace_get_app_vars ()  );
 
    foreach ($pBackup_trace as $pK => $pV )
      $GLOBALS [$pK] = $pV;
 
  } 

  return TRUE;
     
?>
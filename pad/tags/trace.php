<?php

  if ( $pad_walk == 'start' ) {  

    $pad_walk = 'end';

    $pBackup_trace = [];

    foreach ($GLOBALS as $pK2 => $pad_v2 )
      if ( substr($pK2, 0, 9) == 'pTrace' )
        $pBackup_trace [$pK2] = $pad_v2;

    $pTrace           = TRUE;  
    $pTrace_level     = TRUE;  
    $pTrace_occurence = TRUE;  
    $pTrace_fields    = TRUE;  
    $pTrace_eval      = TRUE;  
    $pTrace_eval_type = TRUE;  
    $pTrace_curl      = TRUE;  
    $pTrace_cache     = TRUE;  
    $pTrace_timings   = TRUE;  
    $pTrace_headers   = TRUE;  
    $pTrace_options   = TRUE;  
    $pTrace_sql       = TRUE; 
    $pTrace_explode   = TRUE;
    $pTrace_tag       = TRUE; 

    $pLevelDir = $pTrace_dir . '/trace-' . $pLvl_cnt; 
    $pOccurDir = $pLevelDir;

    $pLevelDir[$p]= $pLevelDir;
    $pOccurDir[$p]= $pOccurDir;

    pFile_put_contents ( $pLevelDir . "/pad-start.json", pTrace_get_pad_vars ()  );
    pFile_put_contents ( $pLevelDir . "/app-start.json", pTrace_get_app_vars ()  );
  
  } else {

    pFile_put_contents ( $pLevelDir . "/pad-end.json", pTrace_get_pad_vars ()  );
    pFile_put_contents ( $pLevelDir . "/app-end.json", pTrace_get_app_vars ()  );
 
    foreach ($pBackup_trace as $pK => $pad_v )
      $GLOBALS [$pK] = $pad_v;
 
  } 

  return TRUE;
     
?>
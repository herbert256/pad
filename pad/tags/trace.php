<?php

  if ( $pad_walk == 'start' ) {  

    $pad_walk = 'end';

    $pad_backup_trace = [];

    foreach ($GLOBALS as $pad_k2 => $pad_v2 )
      if ( substr($pad_k2, 0, 9) == 'pad_trace' )
        $pad_backup_trace [$pad_k2] = $pad_v2;

    $pad_trace           = TRUE;  
    $pad_trace_level     = TRUE;  
    $pad_trace_occurence = TRUE;  
    $pad_trace_fields    = TRUE;  
    $pad_trace_eval      = TRUE;  
    $pad_trace_eval_type = TRUE;  
    $pad_trace_curl      = TRUE;  
    $pad_trace_cache     = TRUE;  
    $pad_trace_timings   = TRUE;  
    $pad_trace_headers   = TRUE;  
    $pad_trace_options   = TRUE;  
    $pad_trace_sql       = TRUE; 
    $pad_trace_explode   = TRUE;
    $pad_trace_tag       = TRUE; 

    $pad_trace_dir_lvl = $pad_trace_dir_base . '/trace-' . $pad_lvl_cnt; 
    $pad_trace_dir_occ = $pad_trace_dir_lvl;

    $pad_parameters [$pad_lvl] ['trace_dir'] = $pad_trace_dir_lvl;
    $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_occ;

    pad_file_put_contents ( $pad_trace_dir_lvl . "/pad-start.json", pad_trace_get_pad_vars ()  );
    pad_file_put_contents ( $pad_trace_dir_lvl . "/app-start.json", pad_trace_get_app_vars ()  );
  
  } else {

    pad_file_put_contents ( $pad_trace_dir_lvl . "/pad-end.json", pad_trace_get_pad_vars ()  );
    pad_file_put_contents ( $pad_trace_dir_lvl . "/app-end.json", pad_trace_get_app_vars ()  );
 
    foreach ($pad_backup_trace as $pad_k => $pad_v )
      $GLOBALS [$pad_k] = $pad_v;
 
  } 

  return TRUE;
     
?>
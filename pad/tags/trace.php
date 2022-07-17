<?php

  if ( $pad_walk == 'start' ) {  

    $pad_walk = 'end';

    $pad_backup_trace = [];

    foreach ($GLOBALS as $pad_k => $pad_v )
      if ( substr($pad_k, 0, 9) == 'pad_trace' )
        $pad_backup_trace [$pad_k] = $pad_v;

    $pad_trace           = TRUE;  
    $pad_trace_level     = TRUE;  
    $pad_trace_occurence = TRUE;  
    $pad_trace_fields    = TRUE;  
    $pad_trace_eval      = TRUE;  
    $pad_trace_file      = TRUE;  
    $pad_trace_curl      = TRUE;  
    $pad_trace_cache     = TRUE;  
    $pad_trace_timings   = TRUE;  
    $pad_trace_headers   = TRUE;  
    $pad_trace_options   = TRUE;  
    $pad_trace_sql       = TRUE;  

    $pad_trace_dir_lvl = $pad_trace_dir_base . '/trace-' . $pad_lvl_cnt; 
    $pad_trace_trace_dir = $pad_trace_dir_occ = $pad_trace_dir_lvl;

    $pad_parameters [$pad_lvl] ['trace_dir'] = $pad_trace_dir_lvl;
    $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_occ;

    pad_file_put_contents ( $pad_trace_dir_lvl . "/pad.json",    pad_trace_get_pad_vars () );
    pad_file_put_contents ( $pad_trace_dir_lvl . "/app.json",    pad_trace_get_app_vars () );
    pad_file_put_contents ( $pad_trace_dir_lvl . "/php.json",    pad_trace_get_php_vars () );
    pad_file_put_contents ( $pad_trace_dir_lvl . "/parent.json", $pad_parameters [$pad_lvl-1] );
    pad_file_put_contents ( $pad_trace_dir_lvl . "/html-base.html", $pad_base[$pad_lvl]        );

    if ( isset($pad_parameters[$pad_lvl+1]) )
      pad_file_put_contents ( $pad_trace_dir_lvl . "/last.json", $pad_parameters [$pad_lvl+1] );

  } else {

    pad_file_put_contents ( $pad_trace_trace_dir . "/pad-after.json", pad_trace_get_pad_vars () );
    pad_file_put_contents ( $pad_trace_trace_dir . "/app-after.json", pad_trace_get_app_vars () );
    pad_file_put_contents ( $pad_trace_trace_dir . "/html-result.html", $pad_result[$pad_lvl] );

    foreach ($pad_backup_trace as $pad_k => $pad_v )
      $GLOBALS [$pad_k] = $pad_v;
 
  } 

  return TRUE;
     
?>
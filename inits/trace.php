<?php

  if ( isset($_REQUEST['pad_trace']) )
    $pad_trace = TRUE;

  if ( ! $pad_trace )
    return;

  $pad_trace_dir_base = "trace/$app/$page/$PADREQID";
  $pad_trace_dir_lvl  = $pad_trace_dir_base;
  $pad_trace_dir_occ  = "$pad_trace_dir_base/tree";

  $pad_parameters [1] ['trace_dir'] = $pad_trace_dir_lvl ; 
  $pad_parameters [1] ['occur_dir'] = $pad_trace_dir_occ ;
      
  pad_file_put_contents ($pad_trace_dir_base . "/start.html", pad_dump_get ('TRACE - start') );
  pad_file_put_contents ($pad_trace_dir_base . "/start.json", pad_json ( pad_track ('000') ) );
  pad_file_put_contents ($pad_trace_dir_base . "/php.json",   pad_dump_get_php_vars ()       );


?>
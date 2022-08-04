<?php

  if ( $pad > 1 ) {
    pad_file_put_contents ( "$pad_trace_dir_lvl/html-result.html", $pad_result[$pad] );
    pad_file_put_contents ( "$pad_trace_dir_lvl/pad-end.json",     pad_trace_get_pad_vars ()  );
    pad_file_put_contents ( "$pad_trace_dir_lvl/app-end.json",     pad_trace_get_app_vars ()  );
  }

  if ($pad > 1) {
    $pad_trace_dir_lvl = $pad_parms [$pad-1] ['trace_dir'];
    $pad_trace_dir_occ = $pad_parms [$pad-1] ['occur_dir'];
  } 
  
?>
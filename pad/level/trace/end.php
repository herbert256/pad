<?php

  if ( $pad > 1 ) {
    pad_file_put_contents ( "$pad_level_dir/html-result.html", $pad_result[$pad] );
    pad_file_put_contents ( "$pad_level_dir/pad-end.json",     pad_trace_get_pad_vars ()  );
    pad_file_put_contents ( "$pad_level_dir/app-end.json",     pad_trace_get_app_vars ()  );
  }

  if ($pad > 1) {
    $pad_level_dir = $pad_parms [$pad-1] ['level_dir'];
    $pad_occur_dir = $pad_parms [$pad-1] ['occur_dir'];
  } 
  
?>
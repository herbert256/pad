<?php

  $pad_trace_dir_occ = $pad_trace_dir_lvl;

  if ( $pad_lvl > 1 and $pad_tag <> 'trace' )
    $pad_trace_dir_occ .= '/occur-' . $pad_occur [$pad_lvl];

  $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_occ ;

  if ( $pad_lvl <= 1 )
    return;

  pad_file_put_contents ( "$pad_trace_dir_occ/data.json",      $pad_current [$pad_lvl]   );
  pad_file_put_contents ( "$pad_trace_dir_occ/pad.json",       pad_trace_get_pad_vars () );
  pad_file_put_contents ( "$pad_trace_dir_occ/app.json",       pad_trace_get_app_vars () );
  pad_file_put_contents ( "$pad_trace_dir_occ/html-base.html", $pad_base[$pad_lvl]      );
  
?>
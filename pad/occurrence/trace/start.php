<?php

  $pad_trace_dir_occ = $pad_trace_dir_lvl;

  if ( $pad > 1 and $pad_tag <> 'trace' )
    $pad_trace_dir_occ .= '/occur-' . $pad_occur [$pad];

  $pad_parms [$pad] ['occur_dir'] = $pad_trace_dir_occ ;

  if ( $pad <= 1 )
    return;

  pad_file_put_contents ( "$pad_trace_dir_occ/data.json",      $pad_current [$pad]   );
  pad_file_put_contents ( "$pad_trace_dir_occ/pad.json",       pad_trace_get_pad_vars () );
  pad_file_put_contents ( "$pad_trace_dir_occ/app.json",       pad_trace_get_app_vars () );
  pad_file_put_contents ( "$pad_trace_dir_occ/html-base.html", $pad_base[$pad]      );
  
?>
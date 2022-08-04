<?php

  $pad_occur_dir = $pad_level_dir;

  if ( $pad > 1 and $pad_tag <> 'trace' )
    $pad_occur_dir .= '/occur-' . $pad_occur [$pad];

  $pad_parms [$pad] ['occur_dir'] = $pad_occur_dir ;

  if ( $pad <= 1 )
    return;

  pad_file_put_contents ( "$pad_occur_dir/data.json",      $pad_current [$pad]   );
  pad_file_put_contents ( "$pad_occur_dir/pad.json",       pad_trace_get_pad_vars () );
  pad_file_put_contents ( "$pad_occur_dir/app.json",       pad_trace_get_app_vars () );
  pad_file_put_contents ( "$pad_occur_dir/html-base.html", $pad_base[$pad]      );
  
?>
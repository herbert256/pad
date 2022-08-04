<?php

  if ( ! $pad_trace_level )
    return;

  $pad_level_dir = $pad_occur_dir;

  if ( $pad > 1)
    $pad_level_dir .= '/tag-' . $pad_lvl_cnt . '-' . $pad_parms[$pad] ['tag'] ;

  $pad_occur_dir = $pad_level_dir;

  $pad_parms [$pad] ['level_dir'] = $pad_level_dir;
  $pad_parms [$pad] ['occur_dir'] = $pad_occur_dir;

  pad_file_put_contents ( "$pad_level_dir/level.json",     $pad_parms [$pad] );  
  pad_file_put_contents ( "$pad_level_dir/pad-start.json", pad_trace_get_pad_vars ()  );
  pad_file_put_contents ( "$pad_level_dir/app-start.json", pad_trace_get_app_vars ()  );
  pad_file_put_contents ( "$pad_level_dir/html-base.html", $pad_base[$pad]        );
  pad_file_put_contents ( "$pad_level_dir/data.json",      $pad_data[$pad]        );

?>
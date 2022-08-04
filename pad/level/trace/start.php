<?php

  if ( ! $pad_trace_level )
    return;

  $pad_trace_dir_lvl = $pad_trace_dir_occ;

  if ( $pad > 1)
    $pad_trace_dir_lvl .= '/tag-' . $pad_cnt . '-' . $pad_parms[$pad] ['tag'] ;

  $pad_trace_dir_occ = $pad_trace_dir_lvl;

  $pad_parms [$pad] ['trace_dir'] = $pad_trace_dir_lvl;
  $pad_parms [$pad] ['occur_dir'] = $pad_trace_dir_occ;

  pad_file_put_contents ( "$pad_trace_dir_lvl/level.json",     $pad_parms [$pad] );  
  pad_file_put_contents ( "$pad_trace_dir_lvl/pad-start.json", pad_trace_get_pad_vars ()  );
  pad_file_put_contents ( "$pad_trace_dir_lvl/app-start.json", pad_trace_get_app_vars ()  );
  pad_file_put_contents ( "$pad_trace_dir_lvl/html-base.html", $pad_base[$pad]        );
  pad_file_put_contents ( "$pad_trace_dir_lvl/data.json",      $pad_data[$pad]        );

?>
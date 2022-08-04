<?php

  if ( ! $pad_trace_level )
    return;

  $pad_trace_dir_lvl = $pad_trace_dir_occ;

  if ( $pad_lvl > 1)
    $pad_trace_dir_lvl .= '/tag-' . $pad_lvl_cnt . '-' . $pad_parameters[$pad_lvl] ['tag'] ;

  $pad_trace_dir_occ = $pad_trace_dir_lvl;

  $pad_parameters [$pad_lvl] ['trace_dir'] = $pad_trace_dir_lvl;
  $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_occ;

  pad_file_put_contents ( "$pad_trace_dir_lvl/level.json",     $pad_parameters [$pad_lvl] );  
  pad_file_put_contents ( "$pad_trace_dir_lvl/pad-start.json", pad_trace_get_pad_vars ()  );
  pad_file_put_contents ( "$pad_trace_dir_lvl/app-start.json", pad_trace_get_app_vars ()  );
  pad_file_put_contents ( "$pad_trace_dir_lvl/html-base.html", $pad_base[$pad_lvl]        );
  pad_file_put_contents ( "$pad_trace_dir_lvl/data.json",      $pad_data[$pad_lvl]        );

?>
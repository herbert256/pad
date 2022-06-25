<?php

  $pad_trace_dir_occ = $pad_trace_dir_lvl;

  $pad_trace_data = [ 
    'level' => $pad_lvl,
    'parms' => $pad_parameters [$pad_lvl],
    'data'  => $pad_data [$pad_lvl]
  ];

  pad_file_put_contents ( "$pad_trace_dir_lvl/level.json", $pad_trace_data           );  
  pad_file_put_contents ( "$pad_trace_dir_lvl/pad.json",   pad_trace_get_pad_vars () );
  pad_file_put_contents ( "$pad_trace_dir_lvl/app.json",   pad_trace_get_app_vars () );
  pad_file_put_contents ( "$pad_trace_dir_lvl/base.html",  $pad_base[$pad_lvl]       );

?>
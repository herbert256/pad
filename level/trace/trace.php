<?php

  $pad_trace_data = [ 
    'level' => $pad_lvl,
    'parms' => $pad_parameters [$pad_lvl],
    'data'  => $pad_data [$pad_lvl]
  ];

  pad_file_put_contents ( "$pad_trace_dir_occ/level.json",      $pad_trace_data            );  
  pad_file_put_contents ( "$pad_trace_dir_occ/parameters.json", $pad_parameters            );
  pad_file_put_contents ( "$pad_trace_dir_occ/data.json",       $pad_data                  );  
  pad_file_put_contents ( "$pad_trace_dir_occ/pad.json",        pad_dump_get_pad_vars ()   );
  pad_file_put_contents ( "$pad_trace_dir_occ/app.json",        pad_dump_get_app_vars ()   );
  pad_file_put_contents ( "$pad_trace_dir_occ/base.html",       $pad_base[$pad_lvl]        );

?>
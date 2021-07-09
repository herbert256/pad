<?php

  if ( $pad_lvl <= 1)
    return;

  $pad_trace_dir_occ = "$pad_trace_dir_lvl/occurrence-" . $pad_occur [$pad_lvl];

  $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_occ ;

  $pad_json = [ 
    'level'      => $pad_lvl,
    'occurrence' => $pad_occur [$pad_lvl],
    'key'        => $pad_key [$pad_lvl]
  ];

  pad_file_put_contents ( "$pad_trace_dir_occ/parameters.json", $pad_json                );
  pad_file_put_contents ( "$pad_trace_dir_occ/data.json",       $pad_current [$pad_lvl]  );  
  pad_file_put_contents ( "$pad_trace_dir_occ/pad.json",        pad_dump_get_pad_vars () );
  pad_file_put_contents ( "$pad_trace_dir_occ/app.json",        pad_dump_get_app_vars () );
  pad_file_put_contents ( "$pad_trace_dir_occ/base.html",       $pad_base[$pad_lvl]      );

?>
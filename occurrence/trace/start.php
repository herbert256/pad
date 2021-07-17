<?php

  $pad_trace_dir_occ = "$pad_trace_dir_lvl/occurrence-" . $pad_occur [$pad_lvl];

  $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_occ ;

  $pad_trace_data = [ 
    'level'      => $pad_lvl,
    'occurrence' => $pad_occur [$pad_lvl],
    'key'        => $pad_key [$pad_lvl],
    'current'    => $pad_current [$pad_lvl]
  ];

  pad_file_put_contents ( "$pad_trace_dir_occ/occurrence.json", $pad_trace_data );

  include PAD_HOME . 'level/trace/trace.php';

?>
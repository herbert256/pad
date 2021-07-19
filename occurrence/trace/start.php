<?php

  $pad_trace_dir_occ = $pad_trace_dir_lvl;

  if ( $pad_lvl > 1 )
    $pad_trace_dir_occ .= '/occurrence-' . $pad_occur [$pad_lvl];

  $pad_parameters [$pad_lvl] ['occur_dir'] = $pad_trace_dir_occ ;

  if ( $pad_lvl <= 1 )
    return;

  $pad_trace_data = [ 
    'level'      => $pad_lvl,
    'occurrence' => $pad_occur [$pad_lvl],
    'key'        => $pad_key [$pad_lvl],
    'current'    => $pad_current [$pad_lvl]
  ];

  pad_file_put_contents ( "$pad_trace_dir_occ/occurrence.json", $pad_trace_data );
  pad_file_put_contents ( "$pad_trace_dir_occ/pad.json",        pad_dump_get_pad_vars ()   );
  pad_file_put_contents ( "$pad_trace_dir_occ/app.json",        pad_dump_get_app_vars ()   );

?>
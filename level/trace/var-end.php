<?php

  $pad_trace_data = [ 
    'field'   => '{' . $pad_between . '}',
    'nr'      => $pad_fld_cnt,
    'source'  => $pad_fld,
    'value'   => $pad_val_base,
    'options' => $pad_opts
  ];

  if ( count ($pad_opts_trace) )
    $pad_trace_data ['changed'] = $pad_opts_trace;
  elseif ( $pad_val <> $pad_val_base )
    $pad_trace_data ['result'] = $pad_val;

  pad_file_put_contents ("$pad_trace_dir_occ/fields/$pad_fld_cnt.$pad_fld_trace.json", pad_json ($pad_trace_data ) );

?>
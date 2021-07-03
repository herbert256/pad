<?php

  pad_trace ("field/end", "nr=$pad_fld_cnt value=$pad_val");

  $pad_trace_data = [ 
    'field'   => $pad_fld,
    'nummer'  => $pad_fld_cnt,
    'start'   => $pad_between,
    'value'   => $pad_val_base,
    'options' => $pad_opts
  ];

  if ( count ($pad_opts_trace) )
    $pad_trace_data ['changed'] = $pad_opts_trace;
  else
    if ( $pad_val <> $pad_val_base )
      $pad_trace_data ['result'] = $pad_val;

  if ( isset ($pad_trace_dir_occ) )
    pad_file_put_contents ("$pad_trace_dir_occ/fields/$pad_fld_cnt.$pad_fld_trace.json", pad_json ($pad_trace_data ) );
  elseif ( isset ($pad_trace_dir_lvl) )
    pad_file_put_contents ("$pad_trace_dir_lvl/fields/$pad_fld_cnt.$pad_fld_trace.json", pad_json ($pad_trace_data ) );
  else      
    pad_file_put_contents ("$pad_trace_dir_base/$pad_fld_cnt.$pad_fld_trace.json", pad_json ($pad_trace_data ) );

?>
<?php

  $pad_fld_cnt++;

  pad_trace ("field/start", "nr=$pad_fld_cnt " . '{' . $pad_between . '}');

  $pad_pipe  = strpos($pad_between, '|');
  $pad_space = strpos($pad_between, ' ');

  if ($pad_pipe and (!$pad_space or $pad_pipe < $pad_space) ) {
    
    $pad_fld  = rtrim(substr($pad_between, 1, $pad_pipe-1));
    $pad_expl = pad_explode(substr($pad_between, $pad_pipe+1), '|');

  } elseif ($pad_space and (!$pad_pipe or $pad_space < $pad_pipe) ) {

    $pad_fld  = rtrim(substr($pad_between, 1, $pad_space-1));
    $pad_expl = pad_explode(substr($pad_between, $pad_space+1), '|');

  } else {

    $pad_fld  = rtrim(substr($pad_between, 1));
    $pad_expl = [];

  }

  if ( substr($pad_fld, 0, 1) == '$' )
    $pad_fld = pad_field_value ($pad_fld);

  if ( ! pad_field_check ( $pad_fld ) )
    pad_trace ("field/error", "nr=$pad_fld_cnt *** NOT FOUND ***");

  $pad_opts = [];

  if ( $pad_first <> '!')
    foreach ( $pad_data_default_start as $pad_v )
      $pad_opts [] = $pad_v;

  foreach ( $pad_expl as $pad_v )
    $pad_opts [] = trim($pad_v);

  if ( $pad_first <> '!')
    foreach ( $pad_data_default_end as $pad_v )
      $pad_opts [] = $pad_v;

  if ( $pad_trace ) {
    $pad_val = $pad_val_base = pad_field_value ($pad_fld);
  } else {
    $pad_val = pad_field_value ($pad_fld);
  }

  $pad_val = pad_var_opts ($pad_val, $pad_opts);

  if ( $pad_first == '!')
    $pad_val = pad_raw ( $pad_val );

  if ( $pad_trace ) {

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
      pad_file_put_contents ("$pad_trace_dir_occ/fields/$pad_fld_cnt.$pad_fld.json", pad_json ($pad_trace_data ) );
    elseif ( isset ($pad_trace_dir_lvl) )
      pad_file_put_contents ("$pad_trace_dir_lvl/fields/$pad_fld_cnt.$pad_fld.json", pad_json ($pad_trace_data ) );
    else      
      pad_file_put_contents ("$pad_trace_dir_base/$pad_fld_cnt.$pad_fld.json", pad_json ($pad_trace_data ) );

  }

  return $pad_val;

?>
<?php

  $pad_fld_cnt++;

  if ($pad_trace) 
    pad_trace ("field/start", "nr=$pad_fld_cnt " . '{' . $pad_between . '}');

  $pad_key[$pad_lvl] = key($pad_data[$pad_lvl]);

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

  $pad_ignore = array_search ('ignore', $pad_expl);
  if ( $pad_ignore !== FALSE ) 
    unset ( $pad_expl[$pad_ignore] );     

  $pad_raw = array_search ('raw', $pad_expl);
  if ( $pad_raw !== FALSE ) 
    unset ( $pad_expl[$pad_raw] );     

  $pad_opts = [];

  if ( $pad_raw === FALSE )
    foreach ( $pad_data_default_start as $pad_v )
      $pad_opts [] = $pad_v;

  foreach ( $pad_expl as $pad_v )
    $pad_opts [] = trim($pad_v);

  if ( $pad_raw === FALSE )
    foreach ( $pad_data_default_end as $pad_v )
      $pad_opts [] = $pad_v;

  $pad_val = pad_field_value ($pad_fld);

  $pad_val = pad_var_opts ($pad_val, $pad_opts);

  if ($pad_trace) 
    pad_trace ("field/end", "nr=$pad_fld_cnt value=$pad_val");

  if ( $pad_ignore )
    return "{ignore}$pad_val{/ignore}";
  else 
    return $pad_val;

?>
<?php

  $pad_fld_cnt++;

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

  $pad_val = pad_field_value ($pad_fld);

  if ( $pad_trace_fields ) 
    include 'trace/start.php';

?>
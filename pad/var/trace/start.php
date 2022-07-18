<?php

  $pad_val_base = $pad_val;

  if ( ! $GLOBALS['pad_trace_errors'] )
    return;

  if ( pad_field_check ( $pad_fld ) ) 
    return;

  $pad_trace_info = [ 
      'field'   => $pad_fld,
      'between' => $pad_between,
  ];

  pad_trace_write_error ( 'Field not found', 'field', $pad_fld_cnt, $pad_trace_info );

?>
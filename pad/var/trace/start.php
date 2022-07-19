<?php

  $pad_val_base = $pad_val;

  if ( ! $GLOBALS['pad_trace_errors'] and ! $GLOBALS['pad_error_dump'] )
    return;

  if ( pad_field_check ( $pad_fld ) ) 
    return;

  $pad_trace_data = [ 
    'field'   => $GLOBALS['pad_fld'],
    'between' => $GLOBALS['pad_between'],
  ];

  pad_trace_write_error ( "Field '$pad_fld' not found", 'field', $GLOBALS['pad_fld_cnt'], $pad_trace_data );

?>
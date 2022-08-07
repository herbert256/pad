<?php

  $pad_val_base = $pad_val;

  if ( ! $GLOBALS['pTrace_errors'] and ! $GLOBALS['pError_dump'] )
    return;

  if ( pField_check ( $pFld ) ) 
    return;

  $pTrace_data = [ 
    'field'   => $GLOBALS['pFld'],
    'between' => $GLOBALS['pBetween'],
  ];

  pTrace_write_error ( "Field '$pFld' not found", 'field', $GLOBALS['pFld_cnt'], $pTrace_data );

?>
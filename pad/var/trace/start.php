<?php

  $pVal_base = $pVal;

  if ( ! $GLOBALS['pTrace'] and ! $GLOBALS['pError_dump'] )
    return;

  if ( pField_check ( $pFld ) ) 
    return;

  pError ( "Field '$pFld' not found" );

  $pTrace_data = [ 
    'field'   => $GLOBALS['pFld'],
    'between' => $GLOBALS['pBetween']
  ];

  pTrace_write_error ( "Field '$pFld' not found", 'field', $GLOBALS['pFldCnt'], $pTrace_data );

?>
<?php

  if ( $GLOBALS ['padInTrace'] )
    return;

  if ( ! $GLOBALS ['padTraceTypes'] ['error'] )
    return;

  padTrace ( 'error', 'info', $error );

  if ( ! $GLOBALS ['padTraceTypes'] ['tree'] ) 
    return;

  $id = uniqid ();
  
  padDumpToDir ( $info, $GLOBALS ['padTraceDir'] . "/ERROR-$id" );

?>
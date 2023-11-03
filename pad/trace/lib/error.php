<?php

  set_error_handler ( 'padErrorThrow' );

  try {

    $padTraceErrorDir = padDumpToDir ( $error );

    padErrorExit ( "Error while in trace\n\n$error\n\n" . padData . $padTraceErrorDir );

  } catch ( Throwable $e ) {

    echo '<pre>' . $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() . "\n\n$error";

    $GLOBALS ['padSkipShutdown']     = TRUE;
    $GLOBALS ['padSkipBootShutdown'] = TRUE;
    
    exit;
    
  }

?>
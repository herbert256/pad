<?php

  set_error_handler ( 'padErrorThrow' );

  try {

    padDumpToDir ( $error, $GLOBALS ['padTraceDir'] . '/ERROR' );

    padErrorExit ( "Error while in trace\n\n$error\n\n" . $GLOBALS ['padTraceDir'] . '/ERROR' );

  } catch ( Throwable $e ) {

    echo '<pre>' . $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() . "\n\n$error";

    $GLOBALS ['padSkipShutdown']     = TRUE;
    $GLOBALS ['padSkipBootShutdown'] = TRUE;
    
    exit;
    
  }

?>
<?php

  set_error_handler ( function ($s, $m, $f, $l) { throw new ErrorException ($m, 0, $s, $f, $l); } );
  $reporting = error_reporting (0);

  try {

    if ( $GLOBALS ['padTraceTree'] )
      $padTraceErrorDir = padTraceDir () . '/ERROR';
    else
      $padTraceErrorDir = 'ERRORS/' . uniqid ();

    padDumpToDir ( $error, $padTraceErrorDir );

    padErrorExit ( "Error while in trace\n\n$error\n\n" . padData . $padTraceErrorDir );

  } catch ( Throwable $e ) {

    echo $e->getMessage() . ' ' . $e->getFile() . ' ' .  $e->getLine();

    echo "$error";

    $GLOBALS ['padSkipShutdown']     = TRUE;
    $GLOBALS ['padSkipBootShutdown'] = TRUE;
    
    exit;
    
  }

?>
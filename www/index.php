<?php

  //  ============================================================================
  //  PAD - (P)HP (A)pplication (D)river
  //  (c) 2004-2022 - Herbert Groot Jebbink - herbert@groot.jebbink.nl
  //  ============================================================================
  //
  //  This is the PAD startup file, the first file that becomes active.
  //
  //  Only this file must be located inside the webservers htdocs directory,
  //  all other PAD files must be stored *OUTSIDE* the webservers htdocs directory.
  //
  //  ============================================================================

  
  // Mark the PAD boot time

  $padTimingsBoot = microtime(true);


  // Start settings

  define ( 'PAD',  '/home/herbert/pad/pad/'  ); // Home of PAD itself
  define ( 'APPS', '/home/herbert/pad/apps/' ); // Home of the PAD applications
  define ( 'DATA', '/home/herbert/data/'     ); // Data locaction, used for logs/cache/errors/etc.

  // End settings


  // Start Boot error handling 

  $padDisplayErrors  = ini_set ('display_errors', 0);
  $padErrorReporting = error_reporting (E_ALL);

  set_error_handler          ( 'padBootErrorHandler'     );
  set_exception_handler      ( 'padBootExceptionHandler' );
  register_shutdown_function ( 'padBootShutdownFunction' );


  // End Boot error handling 
  

  // Go to PAD
  
  include PAD . 'pad.php';


  // PAD boot error handling functions
 
  function padBootError ( $error ) {

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    padBootErrorGo ( $error, $file, $line );

  }

  function padBootErrorHandler ( $type, $error, $file, $line ) {

    padBootErrorGo ( $error, $file, $line );

  }

  function padBootExceptionHandler ( $error ) {

    padBootErrorGo ( $error->getMessage(), $error->getFile(), $error->getLine() );

  }

  function padBootShutdownFunction () {

    if ( isset ( $GLOBALS ['padSkipBootShutdown'] ) )
      return;

    $error = error_get_last ();
 
    if ($error !== NULL)
      padBootErrorGo ( $error['message'], $error['file'], $error['line'] );
 
  }

  function padBootErrorGo ( $error, $file, $line ) {

    $GLOBALS ['padSkipBootShutdown'] = TRUE;
    $GLOBALS ['padSkipShutdown']      = TRUE;

    if ( ! headers_sent () )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    $id = $GLOBALS ['PADREQID'] ?? uniqid (TRUE);

    error_log ( "[PAD] $id - $file:$line $error", 4 );

#    if ( function_exists ( 'padLocal' ) and padLocal () )
      echo "$file:$line $error";
#    else
#      echo "Error: $id";
 
    exit;

  }  


?>
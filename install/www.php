<?php

  $pad_boot = microtime(true);
  
  //  ============================================================================
  //  PAD 10.0  - (P)HP (A)pplication (D)river
  //  (c) 2004-2021 - Herbert Groot Jebbink - herbert@groot.jebbink.nl
  //  ============================================================================
  //
  //  This is the PAD startup file, the first file that becomes active.
  //
  //  Only this file must be located inside the webservers htdocs directory,
  //  all other PAD files must be stored *OUTSIDE* the webservers htdocs directory.
  //
  //  ============================================================================

  define ( 'PAD_HOME', '/home/herbert/pad/'  );  // Home of PAD itself
  define ( 'PAD_APPS', '/home/herbert/apps/' );  // Home of the PAD applications
  define ( 'PAD_DATA', '/home/herbert/data/' );  // Data locaction, used for logs/cache/errors/etc.

  $pad_display_errors  = ini_set ('display_errors', 0);
  $pad_error_reporting = error_reporting (E_ALL);
  
  set_error_handler          ( 'pad_boot_error_handler'     );
  set_exception_handler      ( 'pad_boot_exception_handler' );
  register_shutdown_function ( 'pad_boot_shutdown_function' );

  include PAD_HOME . 'pad.php';

  // Boot error handling.

  function pad_boot_error ( $error ) {
    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );
    pad_boot_error_go ($error, $file, $line);
  }

  function pad_boot_error_handler ( $type, $error, $file, $line ) {
    pad_boot_error_go ($error, $file, $line);
  }

  function pad_boot_exception_handler ( $error ) {
    pad_boot_error_go ($error->getMessage(), $error->getFile(), $error->getLine()) ;
  }

  function pad_boot_shutdown_function () {
    if ( isset ( $GLOBALS['pad_no_boot_shutdown'] ) )
      return;
    $error = error_get_last ();
    if ($error !== NULL)
      pad_boot_error_go ( $error['message'], $error['file'], $error['line'] );
  }

  function pad_boot_error_go ( $error, $file, $line ) {
    $GLOBALS ['pad_no_boot_shutdown'] = TRUE;
    $id = $GLOBALS['PADREQID'] ?? uniqid();
    error_log ( "[PAD] $id $file:$line $error", 4 );
    if ( ! headers_sent () )
      header ( 'HTTP/1.0 500 Internal Server Error' );
    echo "Error: $id";
    exit;   
  }  

?>
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

  $pTimings_boot = microtime(true);


  // Start settings

  define ( 'PAD',  '/home/herbert/pad/pad/'  ); // Home of PAD itself
  define ( 'APPS', '/home/herbert/pad/apps/' ); // Home of the PAD applications
  define ( 'DATA', '/home/herbert/data/'     ); // Data locaction, used for logs/cache/errors/etc.

  // End settings


  // Start Boot error handling 

  $pDisplay_errors  = ini_set ('display_errors', 0);
  $pError_reporting = error_reporting (E_ALL);

  set_error_handler          ( 'pBoot_error_handler'     );
  set_exception_handler      ( 'pBoot_exception_handler' );
  register_shutdown_function ( 'pBoot_shutdown_function' );


  // End Boot error handling 
  

  // Go to PAD
  
  include PAD . 'pad.php';


  // PAD boot error handling functions
 
  function pBoot_error ( $error ) {

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    pBoot_error_go ( $error, $file, $line );

  }

  function pBoot_error_handler ( $type, $error, $file, $line ) {

    pBoot_error_go ( $error, $file, $line );

  }

  function pBoot_exception_handler ( $error ) {

    pBoot_error_go ( $error->getMessage(), $error->getFile(), $error->getLine() );

  }

  function pBoot_shutdown_function () {

    if ( isset ( $GLOBALS['pSkip_boot_shutdown'] ) )
      return;

    $error = error_get_last ();
 
    if ($error !== NULL)
      pBoot_error_go ( $error['message'], $error['file'], $error['line'] );
 
  }

  function pBoot_error_go ( $error, $file, $line ) {

    $GLOBALS ['pSkip_boot_shutdown'] = TRUE;
    $GLOBALS ['pSkip_shutdown']      = TRUE;

    if ( ! headers_sent () )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    $id = $GLOBALS['PADREQID'] ?? uniqid (TRUE);

    error_log ( "[PAD] $id - $file:$line $error", 4 );

    if ( function_exists ( 'pLocal' ) and pLocal () )
      echo "$file:$line $error";
    else
      echo "Error: $id";
 
    exit;

  }  


?>
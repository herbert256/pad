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


  // Start settings

  define ( 'PAD',  '/home/herbert/pad/pad/'  ); // Home of PAD itself
  define ( 'APPS', '/home/herbert/pad/apps/' ); // Home of the PAD applications
  define ( 'DATA', '/home/herbert/data/'     ); // Data locaction, used for logs/cache/errors/etc.

  // End settings


  // Start Boot error handling 

  $pad_display_errors  = ini_set ('display_errors', 0);
  $pad_error_reporting = error_reporting (E_ALL);

  set_error_handler          ( 'pad_boot_error_handler'   );
  set_exception_handler      ( 'pad_boot_error_exception' );
  register_shutdown_function ( 'pad_boot_error_shutdown'  );

  // End Boot error handling 
  

  // Go to PAD
  
  include PAD . 'pad.php';


  // PAD boot error handling functions
 
  function pad_boot_error ( $error ) {

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    pad_boot_error_go ( $error, $file, $line );

  }

  function pad_boot_error_handler ( $type, $error, $file, $line ) {

    pad_boot_error_go ( $error, $file, $line );

  }

  function pad_boot_error_exception ( $error ) {

    pad_boot_error_go ( $error->getMessage(), $error->getFile(), $error->getLine() );

  }

  function pad_boot_error_shutdown () {

    if ( isset ( $GLOBALS['pad_skip_boot_shutdown'] ) )
      return;

    $error = error_get_last ();
 
    if ($error !== NULL)
      pad_boot_error_go ( $error['message'], $error['file'], $error['line'] );
 
  }

  function pad_boot_error_go ( $error, $file, $line ) {

    $GLOBALS ['pad_skip_boot_shutdown'] = TRUE;
    $GLOBALS ['pad_skip_shutdown']      = TRUE;

    if ( ! headers_sent () )
      header ( 'HTTP/1.0 500 Internal Server Error' );

    $id = $GLOBALS['PADREQID'] ?? uniqid (TRUE);

    error_log ( "[PAD] $id - $file:$line $error", 4 );

    if ( function_exists ( 'pad_local' ) and pad_local () )
      echo "$file:$line $error";
    else
      echo "Error: $id";
 
    exit;

  }  


?>
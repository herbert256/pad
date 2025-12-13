<?php


  include PAD . "error/error.php";


  /**
   * Main PAD error handler with full error display and logging.
   *
   * This is the default PAD error action mode. It displays a detailed error
   * page, logs errors, creates dump files, and handles cascading errors gracefully.
   *
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return void Always exits after handling.
   */
  function padErrorGo ( $error, $file, $line ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorTry ( $error, $file, $line );

    } catch (Throwable $e) {

      padErrorStop ( "$file:$line $error", $e );

    }

    restore_error_handler ();

    padExit ( 500 );

  }


  /**
   * Attempts to display and log the error.
   *
   * Checks for double errors, sanitizes the error message, logs to file/dump
   * if configured, and displays the error page via padDump().
   *
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return mixed Return value from padErrorDouble() if double error.
   *
   * @global bool   $padErrorLog    Whether to log errors.
   * @global bool   $padErrorReport Whether to create dump files.
   */
  function padErrorTry ( $error, $file, $line ) {

    if ( isset ( $GLOBALS ['padErrorGo'] ) )
      return padErrorDouble ( $GLOBALS ['padErrorGo'], "$file:$line $error" );
    else
      $GLOBALS ['padErrorGo'] = TRUE;

    $go = padMakeSafe ( "$file:$line $error" );

    $GLOBALS ['padErrorGo']   = $go;
    $GLOBALS ['padErrorFile'] = $file;
    $GLOBALS ['padErrorLine'] = $line;

    if ( function_exists ( 'padInfoTraceError' ) )
      padInfoTraceError ( $go );

    if ( $GLOBALS ['padErrorLog'] )
      padLogError ( $go );

    if ( $GLOBALS ['padErrorReport'] )
      padDumpToDir ( $go );

    padDump ( $go );

  }


  /**
   * Handles a second error occurring during error handling.
   *
   * Called when an error occurs while processing another error.
   * Logs both errors and exits gracefully.
   *
   * @param string $error1 The original error message.
   * @param string $error2 The second error message.
   *
   * @return void Always exits after handling.
   */
  function padErrorDouble ( $error1, $error2 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorDoubleTry ( $error1, $error2 );

    } catch (Throwable $e) {

      padErrorStop ( "$error1\n$error2", $e );

    }

    restore_error_handler ();

    padExit ( 500 );

  } 


  /**
   * Attempts to handle double errors by logging and exiting.
   *
   * Detects triple errors (error during double error handling) and
   * exits immediately. Otherwise logs both errors and displays an error page.
   *
   * @param string $error1 The original error message.
   * @param string $error2 The second error message.
   *
   * @return void
   */
  function padErrorDoubleTry ( $error1, $error2 ) {

    if ( isset ( $GLOBALS ['padErrorDouble'] ) )
      include PAD . 'exits/exit.php';
    else
      $GLOBALS ['padErrorDouble'] = TRUE;

    padErrorLog ( $error1 );
    padErrorLog ( $error2 );

    padErrorExit ( "$error1\n$error2" );

  }


?>
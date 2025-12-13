<?php


  $padDisplayErrors  = ini_set ('display_errors', 0);
  $padErrorReporting = error_reporting (E_ALL);

  set_error_handler          ( 'padBootHandler'   );
  set_exception_handler      ( 'padBootException' );
  register_shutdown_function ( 'padBootShutdown'  );


  /**
   * Reports a boot-time error with automatic file/line detection.
   *
   * Extracts caller information from the debug backtrace and delegates
   * to padBootStop() for error handling.
   *
   * @param string $error The error message to report.
   *
   * @return void
   */
  function padBootError ( $error ) {

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    padBootStop ( $error, $file, $line );

  }


  /**
   * Custom error handler for boot-time PHP errors.
   *
   * Registered with set_error_handler() to catch PHP errors during boot.
   *
   * @param int    $type  The error type/level.
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return void
   */
  function padBootHandler ( $type, $error, $file, $line ) {

    padBootStop ( $error, $file, $line );

  }


  /**
   * Exception handler for boot-time uncaught exceptions.
   *
   * Registered with set_exception_handler() to catch unhandled exceptions during boot.
   *
   * @param Throwable $error The exception or error that was thrown.
   *
   * @return void
   */
  function padBootException ( $error ) {

    padBootStop ( $error->getMessage(), $error->getFile(), $error->getLine() );

  }


  /**
   * Shutdown handler for boot-time fatal errors.
   *
   * Registered with register_shutdown_function() to catch fatal errors
   * that occur during boot. Skips if padBootShutdown is already set.
   *
   * @return void
   */
  function padBootShutdown () {

    if ( isset ( $GLOBALS ['padBootShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL )
      padBootStop ( $error['message'], $error['file'], $error['line'] );

  }


  /**
   * Main boot-time error handler - stops execution and displays error.
   *
   * Sets up error conversion, attempts to display the error, and catches
   * any exceptions that occur during error display. Always exits afterward.
   *
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return void
   */
  function padBootStop ( $error, $file, $line ) {

    $GLOBALS ['padBootShutdown'] = TRUE;

    set_error_handler ( 'padBootStopError' );

    try {

      padBootStopTry ( $error, $file, $line );
      restore_error_handler ();

    } catch (Throwable $e) {

      restore_error_handler ();
      padBootStopCatch ( "$file:$line $error", $e );

    }

    padBootExit ();

  }


  /**
   * Attempts to display a boot-time error.
   *
   * Clears output buffers, sets HTTP 500 status, and displays the error
   * differently for local vs. remote environments. Detects double errors.
   *
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return void
   */
  function padBootStopTry ( $error, $file, $line ) {

    if ( isset ( $GLOBALS ['padBootStop'] ) )
      padBootProblems ( "$file:$line $error", $GLOBALS ['padBootStop'] );

    $GLOBALS ['padBootStop'] = "$file:$line $error";

    $j = ob_get_level ();
    for ( $i = 1; $i <= $j; $i++ )
      ob_get_clean ();

    if ( ! headers_sent () )
      http_response_code(500);

    if ( padLocal () )  padShowErrorLocal  ( $error, $file, $line );
    else                padShowErrorRemote ( $error, $file, $line );

  }


  /**
   * Displays an error for local/development environments.
   *
   * Shows the full error details including file and line number.
   * Outputs plain text for CLI, HTML-escaped text for web.
   *
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return void
   */
  function padShowErrorLocal ( $error, $file, $line ) {

    if ( PHP_SAPI === 'cli' ) {

      echo "$file:$line $error\n";

    } else {

      $msg = htmlspecialchars("$file:$line $error", ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

      echo "\n<pre>{$msg}</pre>";

    }

  }


  /**
   * Displays an error for remote/production environments.
   *
   * Logs the full error with request ID to the error log, but only
   * displays the request ID to the user (hiding sensitive details).
   *
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return void
   */
  function padShowErrorRemote ( $error, $file, $line ) {

      $id = $GLOBALS ['padReqID'] ?? bin2hex(random_bytes(8));

      error_log ( "[PAD] $id $file:$line $error", 4 );

      echo "Error: $id";

  }


  /**
   * Handles exceptions that occur during error display.
   *
   * Last-resort handler when error display itself throws an exception.
   * Attempts to log both errors, then exits.
   *
   * @param string    $error The original error message.
   * @param Throwable $e     The exception thrown during error handling.
   *
   * @return void
   */
  function padBootStopCatch  ( $error, $e ) {

    set_error_handler ( 'padBootStopError' );

    try {

      $error2 = $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() ;

      padBootProblems( $error2, $error );

    } catch (Throwable $e2) {

      // Ignore Errors

    }

    include PAD . 'exits/exit.php';

  }


  /**
   * Converts PHP errors to exceptions during error handling.
   *
   * Used as a temporary error handler to convert errors to exceptions
   * that can be caught in try/catch blocks during error display.
   *
   * @param int    $severity The error severity level.
   * @param string $message  The error message.
   * @param string $filename The file where the error occurred.
   * @param int    $lineno   The line number where the error occurred.
   *
   * @throws ErrorException Always throws to convert error to exception.
   */
  function padBootStopError ( $severity, $message, $filename, $lineno ) {

    throw new ErrorException ( $message, 0, $severity, $filename, $lineno );

  }


  /**
   * Handles multiple cascading errors during boot.
   *
   * Called when a second error occurs during error handling.
   * Displays both errors (in local mode) then exits.
   *
   * @param string $error1 The first error message.
   * @param string $error2 The second error message.
   *
   * @return void
   */
  function padBootProblems ( $error1, $error2 ) {

    if ( padLocal () )
      echo "<pre><br>$error2<br>$error1</pre>";

    padBootExit ();

  }


  /**
   * Exits the application after a boot-time error.
   *
   * Uses padExit() if available (runtime), otherwise includes
   * the exit script directly (very early boot).
   *
   * @return void
   */
  function padBootExit () {

    if ( function_exists ( 'padExit') )
      padExit ( 500 );
    else
      include PAD . 'exits/exit.php';

  }


  /**
   * Checks if the request is from a local/development environment.
   *
   * Returns true for CLI mode or if the server/client address matches
   * known local addresses (localhost, 127.0.0.1, ::1).
   *
   * @return bool TRUE if local environment, FALSE for remote.
   */
  function padLocal () {

    if ( PHP_SAPI === 'cli' )
      return true;

    $local  = [ 'localhost', 'penguin.linux.test', '127.0.0.1', '::1' ];
    $server = [ $_SERVER ['REMOTE_ADDR'] ?? '',  $_SERVER ['SERVER_NAME'] ?? '' ];

    foreach ( $local as $check )
      if ( in_array( $check, $server) )
        return TRUE;

    return FALSE;

  }


?>
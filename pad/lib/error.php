<?php


  /**
   * Error handler that converts errors to exceptions.
   *
   * Used temporarily during error handling to convert PHP errors to
   * catchable exceptions. Only throws for enabled error types.
   *
   * @param int    $type  The error type/level.
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number.
   *
   * @throws ErrorException If error type is enabled in error_reporting.
   */
  function padErrorThrow ( $type, $error, $file, $line ) {

    if ( ( error_reporting() & $type ) )
      throw new \ErrorException ( $error, 0, $type, $file, $line);

  }


  /**
   * Reports a PAD framework error.
   *
   * Extracts caller location from backtrace and delegates to the
   * configured error handler. Returns FALSE for use in return statements.
   *
   * @param string $error The error message.
   *
   * @return bool Always returns FALSE.
   */
  function padError ($error) {

    extract ( debug_backtrace (DEBUG_BACKTRACE_IGNORE_ARGS, 1) [0] );

    padErrorGo ( 'PAD: ' . $error, $file, $line );

    return FALSE;

  }


  /**
   * Restores PHP's default error handlers after boot phase.
   *
   * Removes the boot-time error/exception handlers and marks the
   * boot shutdown as complete.
   *
   * @return void
   */
  function padErrorRestoreBoot () {

    restore_error_handler ();
    restore_exception_handler ();
    $GLOBALS ['padBootShutdown'] = TRUE;

  }


  /**
   * Formats an exception as a string with file, line, and message.
   *
   * @param Throwable $e The exception to format.
   *
   * @return string Formatted string "file:line message".
   */
  function padErrorGet ( $e ) {

    return $e->getFile() . ':' .  $e->getLine() . ' ' . $e->getMessage() ;

  }


  /**
   * Handles a fatal error with exception context.
   *
   * Logs both the error message and exception details, displays
   * error output, and terminates with HTTP 500.
   *
   * @param string    $error The error message.
   * @param Throwable $e     The exception that triggered the stop.
   *
   * @return void Never returns - calls padExit(500).
   */
  function padErrorStop ( $error, $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorStopTry ( $error, $e );

    } catch (Throwable $e2) {

      padErrorStopCatch ( $error, $e, $e2 );

    }

    restore_error_handler ();

    padExit ( 500 );

  }


  /**
   * Attempts to log and display a fatal error.
   *
   * Extracts error info from exception, logs both errors,
   * and displays combined error output.
   *
   * @param string    $error The original error message.
   * @param Throwable $e     The exception with additional context.
   *
   * @return void
   */
  function padErrorStopTry ( $error, $e ) {

    $error2 = padErrorGet ( $e );

    padErrorLog ( $error );
    padErrorLog ( $error2 );

    padErrorExit ( "$error\n$error2" );

  }


  /**
   * Fallback handler when padErrorStopTry fails.
   *
   * Writes errors to file instead of log, then displays and exits.
   *
   * @param string    $error1 The original error message.
   * @param Throwable $e2     The first exception.
   * @param Throwable $e3     Exception from failed stop attempt.
   *
   * @return void Never returns - calls padExit(500).
   */
  function padErrorStopCatch ( $error1, $e2, $e3 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $error2 = padErrorGet ( $e2 );
      $error3 = padErrorGet ( $e3 );

      padErrorFile ( $error1 );
      padErrorFile ( $error2 );
      padErrorFile ( $error3 );

      padErrorExit ( "$error1\n$error2\n$error3" );

    } catch (Throwable $e4) {

      padErrorStopCatchCatch ( $error1, $e2, $e3, $e4 );

    }

    restore_error_handler ();
    
    padExit ( 500 );

  }


  /**
   * Second fallback when file logging also fails.
   *
   * Outputs errors to console and sends HTTP 500 header directly.
   *
   * @param string    $error1 The original error message.
   * @param Throwable $e2     First exception.
   * @param Throwable $e3     Second exception.
   * @param Throwable $e4     Third exception (from file logging).
   *
   * @return void Never returns - calls padExit(500).
   */
  function padErrorStopCatchCatch ( $error1, $e2, $e3, $e4 ) {

    set_error_handler ( 'padErrorThrow' );

    try {
 
      if ( ! headers_sent () )
        header ( 'HTTP/1.0 500 Internal Server Error' );
 
      $error2 = padErrorGet ( $e2 );
      $error3 = padErrorGet ( $e3 );
      $error4 = padErrorGet ( $e4 );

      padErrorConsole ( $error1 );
      padErrorConsole ( $error2 );
      padErrorConsole ( $error3 );
      padErrorConsole ( $error4 );

      padErrorExit ( "$error1\n$error2\n$error3\n$error4" );

    } catch (Throwable $e5) {

      padErrorStopCatchCatchCatch ( $error1, $e2, $e3, $e4, $e5 );

    }

    restore_error_handler ();

    padExit ( 500 );

  }
  

  /**
   * Final fallback - last resort error display.
   *
   * Simply attempts to display all accumulated errors and exit.
   * Silently ignores any further errors.
   *
   * @param string    $error1 The original error message.
   * @param Throwable $e2     First exception.
   * @param Throwable $e3     Second exception.
   * @param Throwable $e4     Third exception.
   * @param Throwable $e5     Fourth exception.
   *
   * @return void Never returns - calls padExit(500).
   */
  function padErrorStopCatchCatchCatch ( $error1, $e2, $e3, $e4, $e5 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $error2 = padErrorGet ( $e2 );
      $error3 = padErrorGet ( $e3 );
      $error4 = padErrorGet ( $e4 );
      $error5 = padErrorGet ( $e5 );
      
      padErrorExit ( "$error1\n$error2\n$error3\n$error4\n$error5" );

    } catch (Throwable $e6) {

      // Ignoring errors

    }
 
    restore_error_handler ();
   
    padExit ( 500 );

  }
 

  /**
   * Logs an error message via padLogError.
   *
   * Falls back to file logging if padLogError fails.
   *
   * @param string $info The error information to log.
   *
   * @return void
   */
  function padErrorLog ( $info ) {

    if ( ! $info )
      return;

    set_error_handler ( 'padErrorThrow' );

    try {

      padLogError ( $info );
    
    } catch (Throwable $e) {
    
      padErrorLogCatch ( $info, $e );
    
    }

    restore_error_handler ();

  }


  /**
   * Fallback handler when padErrorLog fails.
   *
   * Writes errors directly to file instead of using log system.
   *
   * @param string    $error The original error message.
   * @param Throwable $e2    Exception from failed log attempt.
   *
   * @return void
   */
  function padErrorLogCatch ( $error, $e2 ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorFile ( $error );
      padErrorFile ( padErrorGet ( $e2 ) );

    } catch ( Throwable $e3 ) {

      // Ignore errors

    }

    restore_error_handler ();

  }


  /**
   * Writes error info directly to error_log.txt file.
   *
   * Prepends request ID and sanitizes the message before writing.
   *
   * @param string $info The error information to write.
   *
   * @return void
   */
  function padErrorFile ( $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      $log = padID () . ' - ' . padMakeSafe ( $info );

      padFilePut ( 'error_log.txt', $log, true );

    } catch (Throwable $e) {

      padErrorFileCatch ( $e, $info );
  
    }

    restore_error_handler ();
    
  }


  /**
   * Fallback when file logging fails.
   *
   * Outputs errors to console instead.
   *
   * @param Throwable $e    Exception from failed file write.
   * @param string    $info The original error information.
   *
   * @return void
   */
  function padErrorFileCatch ( $e, $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorConsole ( $info );
      padErrorConsole ( padErrorGet ( $e ) );

    } catch (Throwable $e2) {

      // Ignore errors

    }

    restore_error_handler ();

  }

  
  /**
   * Outputs error message to console/browser.
   *
   * Shows detailed error on local environments, generic message
   * on production to avoid information disclosure.
   *
   * @param string $info The error information to display.
   *
   * @return void
   */
  function padErrorConsole ( $info ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( padLocal () )
        echo "<pre>\nError: $info</pre>";
      else
        echo '<pre>Unknow error occurred.</pre>';
    
    } catch (Throwable $e) {
    
      // Ignore errors
    
    }

    restore_error_handler ();

  }


  /**
   * Displays error and terminates execution.
   *
   * Clears output buffers, shows error (detailed on local,
   * request ID only on production), and exits with HTTP 500.
   *
   * @param string $error The error message to display.
   *
   * @return void Never returns - calls padExit(500).
   */
  function padErrorExit ( $error ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padEmptyBuffers ( $buffer );

      if ( padLocal () )
        echo "\n<pre>$error\n\n$buffer</pre>";
      else
        echo 'Error: ' . padID ();
    
    } catch (Throwable $e) {
    
      padErrorExitCatch ( $error, $e );
    
    }

    restore_error_handler ();

    padExit ( 500 );

  }


  /**
   * Fallback when padErrorExit fails.
   *
   * Attempts console output, falls back to "oops" on failure.
   *
   * @param string    $error The error message.
   * @param Throwable $e     Exception from failed exit attempt.
   *
   * @return void Never returns - calls padExit(500).
   */
  function padErrorExitCatch ( $error, $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorConsole ( $error );
      padErrorConsole ( padErrorGet ( $e ) );

    } catch (Throwable $e2) {

      echo 'oops';

    }

    restore_error_handler ();

    padExit ( 500 );

  }


?>
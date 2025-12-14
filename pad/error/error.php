<?php


  padErrorReporting   ( $padErrorLevel );
  padErrorRestoreBoot ();

  set_error_handler          ( 'padErrorHandler'   );
  set_exception_handler      ( 'padErrorException' );
  register_shutdown_function ( 'padErrorShutdown'  );


  /**
   * Sets the PHP error reporting level using named presets.
   *
   * Supports preset levels: 'none', 'error', 'warning', 'notice', 'all'.
   * Each level includes errors from the previous levels.
   *
   * @param string $level The error level preset name.
   *
   * @return void
   */
  function padErrorReporting ( $level ) {

    $none    = (int) 0;
    $error   = (int) $none    | E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING |
                                E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED ;

    error_reporting ( $$level );

  }


  /**
   * Custom runtime error handler for PHP errors.
   *
   * Checks if the error type is enabled in error_reporting and
   * delegates to padErrorGo() for handling. Always returns TRUE
   * to prevent PHP's internal error handler from running.
   *
   * @param int    $type  The error type/level.
   * @param string $error The error message.
   * @param string $file  The file where the error occurred.
   * @param int    $line  The line number where the error occurred.
   *
   * @return bool Always returns TRUE.
   */
  function padErrorHandler ( $type, $error, $file, $line ) {

    if ( error_reporting() & $type )
      padErrorGo ( 'ERROR: ' . $error, $file, $line );

    return TRUE;

  }


  /**
   * Runtime exception handler for uncaught exceptions.
   *
   * Stores exception details in globals for debugging and delegates
   * to padErrorGo() for handling.
   *
   * @param Throwable $padException The exception that was thrown.
   *
   * @return mixed Return value from padErrorGo().
   *
   * @global Throwable $padException      The exception object.
   * @global string    $padExceptionFile  File where exception was thrown.
   * @global int       $padExceptionLine  Line number of exception.
   * @global string    $padExceptionError Exception message.
   * @global string    $padExceptionText  Formatted exception string.
   */
  function padErrorException ( $padException ) {

    $GLOBALS ['padException'] = $padException;

    global $padExceptionFile, $padExceptionLine, $padExceptionError, $padExceptionText;

    $padExceptionFile  = $padException->getFile();
    $padExceptionLine  = $padException->getLine();
    $padExceptionError = $padException->getMessage();
    $padExceptionText  = "$padExceptionFile:$padExceptionLine $padExceptionError" ;

    return padErrorGo (
             'EXCEPTION: ' .
             $padException->getMessage() , $padException->getFile(), $padException->getLine()
    );

  }


  /**
   * Shutdown handler for runtime fatal errors.
   *
   * Checks for fatal errors after script execution and handles them.
   * Skips if padSkipShutdown is set (clean exit).
   *
   * @return mixed Return value from padErrorGo() if error, void otherwise.
   */
  function padErrorShutdown () {

    if ( isset ( $GLOBALS ['padSkipShutdown'] ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL )
      return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );

  }


?>
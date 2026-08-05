<?php

  // Installs PAD's own runtime error handlers, taking over from the boot net in error/boot.php.
  //
  // Included by each error/types/<action>.php that wants them (all but boot and php), so the
  // handlers are live before that action defines padErrorGo. padErrorReporting maps the
  // $padErrorLevel setting - none, error, warning, notice, all - onto error_reporting.
  //
  // padErrorHandler, padErrorException and padErrorShutdown catch a PHP error, an uncaught
  // throwable and a fatal at shutdown; each one calls padErrorGo, which is the hook the chosen
  // $padErrorAction supplies and which decides whether the request continues or ends. The
  // exception path also records $padException* for the dump, and the shutdown hook stands down
  // once exits/exit.php has set $padSkipShutdown.

  padErrorReporting   ( $padErrorLevel );
  padErrorRestoreBoot ();

  set_error_handler          ( 'padErrorHandler'   );
  set_exception_handler      ( 'padErrorException' );
  register_shutdown_function ( 'padErrorShutdown'  );

  function padErrorReporting ( $level ) {

    $none    = (int) 0;
    $error   = (int) $none    | E_ERROR | E_USER_ERROR | E_CORE_ERROR | E_COMPILE_ERROR | E_PARSE;
    $warning = (int) $error   | E_RECOVERABLE_ERROR | E_WARNING | E_USER_WARNING |
                                E_CORE_WARNING | E_COMPILE_WARNING;
    $notice  = (int) $warning | E_NOTICE | E_USER_NOTICE;
    $all     = (int) $notice  | E_DEPRECATED | E_USER_DEPRECATED ;

    error_reporting ( $$level );

  }

  function padErrorHandler ( $type, $error, $file, $line ) {

    if ( error_reporting() & $type )
      padErrorGo ( 'ERROR: ' . $error, $file, $line );

    return TRUE;

  }

  function padErrorException ( $e ) {

    global $padException, $padExceptionError, $padExceptionFile, $padExceptionLine, $padExceptionText;

    $padException      = $e;
    $padExceptionFile  = $e->getFile();
    $padExceptionLine  = $e->getLine();
    $padExceptionError = $e->getMessage();
    $padExceptionText  = "$padExceptionFile:$padExceptionLine $padExceptionError" ;

    return padErrorGo ( "EXCEPTION: $padExceptionError", $padExceptionFile, $padExceptionLine );

  }

  function padErrorShutdown () {

    global $padSkipShutdown;

    if ( isset ( $padSkipShutdown ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL )
      return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );

  }

?>
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

    // A word that is no level failed every request with "Undefined variable $warnings" - or,
    // for the word level, with a TypeError - naming nothing a configuration says. The boot
    // handlers still stand when this runs, and report it as inits/configCheck.php reports
    // the other closed words.

    $levels = compact ( 'none', 'error', 'warning', 'notice', 'all' );

    if ( ! is_string ( $level ) or ! isset ( $levels [$level] ) )
      throw new \ErrorException ( "PAD: there is no error level named '" . ( is_scalar ( $level ) ? $level : gettype ( $level ) )
                                  . "' - none, error, warning, notice or all" );

    error_reporting ( $levels [$level] );

  }

  // While an error page renders (lib/errorPage.php) a PHP error is that page's failure,
  // thrown back to it, never a report of its own - which would ask for an error page again.

  function padErrorHandler ( $type, $error, $file, $line ) {

    if ( ( $GLOBALS ['padErrorPageBusy'] ?? FALSE ) and ( error_reporting() & $type ) )
      throw new \ErrorException ( $error, 0, $type, $file, $line );

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

    padErrorGo ( "EXCEPTION: $padExceptionError", $padExceptionFile, $padExceptionLine );

    // An action that carries on - log, ignore, dump - comes back here, but an uncaught
    // throwable has unwound the page and PHP ends the request once this handler returns:
    // with nothing rendered, it ended as an empty 200. It ends as the failure it is.

    padExit ( 500 );

  }

  function padErrorShutdown () {

    global $padSkipShutdown;

    if ( isset ( $padSkipShutdown ) )
      return;

    $error = error_get_last ();

    if ( $error !== NULL ) {
      padBootRoom ( $error );
      return padErrorGo ( 'SHUTDOWN: ' . $error['message'] , $error['file'], $error['line'] );
    }

  }

?>

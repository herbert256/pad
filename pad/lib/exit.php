<?php

  // Ends a request cleanly with HTTP status $stop. Applications and the engine call
  // padExit rather than exit/die, so the session is closed, buffers are flushed and
  // headers are sent before exits/exit.php finally calls exit.
  //
  // padExit        wraps the shutdown in an error handler and always reaches
  //                exits/exit.php, even if the shutdown itself throws
  // padExitTry     the actual work: close session, empty output buffers, send web
  //                headers, and let the info subsystem write its report
  // padExitCatch   reports a throwable through padErrorGo, swallowing a second failure
  // padExitDouble  guards a re-entrant padExit (padSecondTime) by exiting straight away
  //                instead of tearing the request down twice

  function padExit ( $stop = 200 ) {

    global $exit;

    set_error_handler ( 'padErrorThrow' );

    try {

      padExitTry ( $stop );

    } catch (Throwable $e) {

      $exit = $e;

      padExitCatch ( $e );

    }

    restore_error_handler ();

    include PAD . 'exits/exit.php';

  }

  function padExitTry ( $stop ) {

    global $padInfoStarted, $padOutputType;

    if ( padSecondTime ( 'exit' ) )
      return padExitDouble ( $stop );

    padCloseSession ();

    padEmptyBuffers ( $padIgnored );

    if ( $padOutputType == 'web' )
      padWebHeaders ( $stop );

    if ( isset ( $padInfoStarted ) and ! padSecondTime ( 'exitInfo' ) )
      include PAD . 'info/end/config.php';

  }

  function padExitCatch ( $e ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      padErrorGo ( $e->getMessage(), $e->getFile(), $e->getLine() );

    } catch (Throwable $e) {

    }

    restore_error_handler ();

  }

  function padExitDouble ( $stop ) {

    set_error_handler ( 'padErrorThrow' );

    try {

      if ( padSecondTime ( 'exitDouble' ) )
        include PAD . 'exits/exit.php';

    } catch (Throwable $e) {

    }

    restore_error_handler ();

  }

?>
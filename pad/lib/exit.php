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
  // padRefuse      turns the request away with a client error status and one plain line
  //                saying why - a POST without its CSRF token, a page a guard keeps out

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

    if ( in_array ( $padOutputType, [ 'web', 'json', 'csv' ], TRUE ) )
      padWebHeaders ( $stop );

    if ( isset ( $padInfoStarted ) and ! padSecondTime ( 'exitInfo' ) )
      include PAD . 'info/end/config.php';

    if ( ( $GLOBALS ['padCoverageRun'] ?? '' ) and ! padSecondTime ( 'exitCoverage' ) )
      padCoverageWrite ( $stop );

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

  // A request the application must not run - or must not finish running - is answered
  // with the status and a short plain-text reason, the way a missing page is answered 404
  // in inits/page.php: whatever the page had produced so far is dropped and the request
  // ends. It used to need padExit, whose answer to any status but 200 is an empty body -
  // the visitor saw a blank page and no reason.
  //
  // The session is closed as it stands, not through padCloseSession: a refusal can come
  // before inits/parms.php has taken the $padSessionVars names in, and writing the globals
  // back then would empty every one of them - a stale form would log the visitor out.
  //
  // An application with an error page for the status - _errors/403.pad, lib/errorPage.php -
  // answers with that page instead, the line as its $message; rendered first, as the
  // buffers it would otherwise land in are emptied after.

  function padRefuse ( $stop, $text ) {

    $page = padErrorPageRender ( $stop, $text );

    padEmptyBuffers ( $ignored );

    if ( ! headers_sent () ) {
      // The status line with its phrase, not the code alone: Apache under mod_php has no
      // phrase of its own for some codes padAbort takes - 418 among them - and sent 500
      // in their place.
      header ( "HTTP/1.1 $stop " . padAbortPhrase ( $stop ), TRUE, $stop );
      header ( 'Content-Type: ' . ( $page === FALSE ? 'text/plain' : 'text/html' ) . '; charset=UTF-8' );
      header ( 'Cache-Control: no-cache, no-store' );
      if ( ( $GLOBALS ['padOutputType'] ?? 'web' ) == 'web' )
        padSecurityHeaders ();
    }

    echo ( $page === FALSE ) ? $text : $page;

    if ( session_status () === PHP_SESSION_ACTIVE )
      session_write_close ();

    include PAD . 'exits/exit.php';

  }

?>

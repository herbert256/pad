<?php

  // Custom error pages: an application answers a status the request cannot go on from -
  // a page not found, a guard's refusal, a padAbort, a missing CSRF token, maintenance, a
  // failure under the 'pad' error action - with a template of its own instead of one plain
  // line, keeping the status.
  //
  //   _errors/404.pad        the page for one status (.html works as well)
  //   _errors/4xx.pad        every client error without a page of its own; 5xx.pad likewise
  //   _errors/_inits.pad     the frame of the error pages, with @page@ - and _exits.pad
  //
  // The template sees $status (404), $message (the line the plain answer would have been -
  // with the detail a local request gets) and $page (the page that was asked for); a 500
  // has $id too, the request's id the plain answer shows. It renders in its own frame from
  // _errors/, not in the application's _inits.pad: the request ended before the page was
  // built, and what refused it - a guard, an _inits.php, a database that is down - is what
  // the application's frame would run again. The application's _lib is not there for the
  // same reason; the engine's tags and functions are.
  //
  // $padErrorPages switches it off; an application without an _errors/ directory answers as
  // it always did. Only the web output type gets a page - a json or csv answer, and a
  // request without an open level (a failure before the engine got going), get the plain
  // line. A page that fails itself - a typo the strict check finds, a PHP warning, an error
  // page asking for an error page - is set aside: the failure goes to the log and the plain
  // line is the answer, so an error page never loops.
  //
  // padErrorPageFile    the template for a status: the status's own, then its class's, ''
  //                     when there is none or the request cannot have one
  // padErrorPageFileAny the same, whatever the settings - asked before they are read
  // padErrorPageRender  the template rendered, FALSE when there is none or it failed
  // padNotFound         the 404 of a page that is not there - inits/page.php, after the
  //                     configuration (inits/notFound.php) when the application has a page
  //                     for it

  function padErrorPageFile ( $status ) {

    if ( empty ( $GLOBALS ['padErrorPages'] ) or ( $GLOBALS ['padErrorPageBusy'] ?? FALSE ) )
      return '';

    if ( ( $GLOBALS ['padOutputType'] ?? 'web' ) != 'web' )
      return '';

    return padErrorPageFileAny ( $status );

  }

  function padErrorPageFileAny ( $status ) {

    $status = (int) $status;

    if ( $status < 400 or $status > 599 or ! defined ( 'APP' ) )
      return '';

    $dir = APP . '_errors/';

    foreach ( [ $status, intdiv ( $status, 100 ) . 'xx' ] as $name )
      foreach ( [ 'pad', 'html' ] as $ext )
        if ( is_file ( "$dir$name.$ext" ) )
          return "$dir$name.$ext";

    return '';

  }

  function padErrorPageRender ( $status, $message ) {

    global $pad, $padOut, $padErrorTry, $padErrorPageBusy;

    $file = padErrorPageFile ( $status );

    if ( ! $file )
      return FALSE;

    // The page renders in a nested pass, which opens on the level the request stands on:
    // before inits/level.php opened the root level there is none to open on.

    if ( ! isset ( $pad ) or $pad < 0 or ! isset ( $padOut [$pad] ) )
      return FALSE;

    $dir    = APP . '_errors/';
    $source = (string) file_get_contents ( $file );
    $inits  = is_file ( "{$dir}_inits.pad" ) ? (string) file_get_contents ( "{$dir}_inits.pad" ) : '';
    $exits  = is_file ( "{$dir}_exits.pad" ) ? (string) file_get_contents ( "{$dir}_exits.pad" ) : '';

    if ( $inits !== '' or $exits !== '' ) {
      if ( str_contains ( $inits . $exits, '@page@' ) )
        $source = str_replace ( '@page@', $source, $inits . $exits );
      else
        $source = $inits . $source . $exits;
    }

    $GLOBALS ['status']  = (int) $status;
    $GLOBALS ['message'] = (string) $message;
    $GLOBALS ['page']    = (string) ( $GLOBALS ['padNotFoundName'] ?? ( ( $GLOBALS ['padPageAsked'] ?? '' ) !== '' ? $GLOBALS ['padPageAsked'] : ( $GLOBALS ['padPage'] ?? '' ) ) );

    if ( (int) $status == 500 and function_exists ( 'padID' ) )
      $GLOBALS ['id'] = padID ();

    // An error inside the page comes back as a throwable (padErrorHook, padErrorHandler)
    // instead of being reported, the try guards stand aside, and whatever the pass echoes
    // is dropped.

    $padErrorPageBusy = TRUE;
    $tryWas           = $padErrorTry ?? FALSE;
    $padErrorTry      = FALSE;
    $level            = ob_get_level ();

    set_error_handler ( 'padErrorThrow' );

    ob_start ();

    try {

      $GLOBALS ['padSectionsNext'] = TRUE;

      $out = padUnprotect ( padUnescape ( padCode ( $source ) ) );

    } catch ( Throwable $e ) {

      if ( function_exists ( 'padLogError' ) )
        padLogError ( 'the error page ' . str_replace ( APPS, '', $file ) . ' failed: ' . padErrorGet ( $e ) );

      $out = FALSE;

    } finally {

      while ( ob_get_level () > $level )
        ob_end_clean ();

      restore_error_handler ();

      $padErrorTry      = $tryWas;
      $padErrorPageBusy = FALSE;

    }

    return $out;

  }

  // The 404 of a page that is not there, as inits/page.php answered it: one plain line,
  // the name shown to this machine's own requests only - or the application's error page,
  // through padRefuse, when it has one and the root level is open (inits/notFound.php
  // opens it for the purpose).

  function padNotFound ( $name ) {

    $text = padLocal () ? "Page '" . padMakeSafe ( $name, 100 ) . "' not found" : 'Page not found';

    if ( padErrorPageFile ( 404 ) and isset ( $GLOBALS ['padOut'] [0] ) ) {
      $GLOBALS ['padNotFoundName'] = (string) $name;
      padRefuse ( 404, $text );
    }

    while ( ob_get_level () )
      ob_end_clean ();

    if ( ! headers_sent () ) {
      http_response_code ( 404 );
      header ( 'Content-Type: text/plain; charset=UTF-8' );
    }

    echo $text;

    $stop = 404;
    include PAD . 'exits/exit.php';

  }

?>

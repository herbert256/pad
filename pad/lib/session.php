<?php

  // Request-lifecycle odds and ends: session shutdown, identity and one-shot guards.
  //
  // padInfo           a summary array of the request (session, request and parent ids,
  //                   page, status, length, start and end time, etag) for logs and dumps
  // padInclude        TRUE when this request is a nested include rather than a page
  // padSecondTime     one-shot guard: FALSE the first time an id is seen, TRUE after, so
  //                   shutdown steps cannot run twice
  // padCloseSession   writes the globals named in $padSessionVars back into $_SESSION and
  //                   closes the session early, so a slow page does not block the user's
  //                   other requests; wrapped so a failure here cannot break the exit
  // padID             the request id, or a fresh uniqid if the request never got one
  // padLogError       sends a message to the SAPI error log, tagged with that id

  function padInfo () {

    global $padEtag, $padLen, $padPage, $padRefID, $padReqID, $padSesID, $padStop;

    return [
      'session' => $padSesID ?? '',
      'request' => $padReqID ?? '',
      'parent'  => $padRefID ?? '',
      'page'    => $padPage  ?? '',
      'stop'    => $padStop  ?? '',
      'length'  => $padLen   ?? '',
      'start'   => $_SERVER ['REQUEST_TIME_FLOAT'] ?? 0 ,
      'end'     => microtime (true),
      'etag'    => $padEtag  ?? ''
    ];

  }

  function padInclude () {

    global $padInclude;

    if ( isset ( $padInclude ) and $padInclude )
      return TRUE;
    else
      return FALSE;

  }

  function padSecondTime ( $id ) {

    if ( isset ( $GLOBALS ["padSecond$id"] ) )
      return TRUE;

    $GLOBALS ["padSecond$id"] = TRUE;

    return FALSE;

  }

  function padCloseSession () {

    set_error_handler ( 'padErrorThrow' );

    try {

      padCloseSessionTry ();

    } catch (Throwable $e) {

    }

    restore_error_handler ();

  }

  function padCloseSessionTry () {

    if ( ! isset ( $GLOBALS ['padSessionStarted'] ) or padSecondTime ( 'closeSession' ) )
      return;

    foreach ( $GLOBALS ['padSessionVars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];

    session_write_close ();

  }

  function padID () {

    global $padReqID;

    return $padReqID ?? uniqid (TRUE);

  }

  function padLogError ( $error ) {

    error_log ( '[PAD] ' . padID () . ' ' . padMakeSafe ( $error ), 4 );

  }

?>
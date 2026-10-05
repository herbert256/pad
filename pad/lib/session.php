<?php

  // Request-lifecycle odds and ends: session shutdown, identity and one-shot guards.
  //
  // padInfo           a summary array of the request (session, request and parent ids,
  //                   page, status, length, start and end time, etag) for logs and dumps
  // padInclude        TRUE when this request is a nested include rather than a page
  // padSecondTime     one-shot guard: FALSE the first time an id is seen, TRUE after, so
  //                   shutdown steps cannot run twice
  // padCloseSession   writes the globals named in $padSessionVars back into $_SESSION (an
  //                   unset or NULL one is removed from it) and
  //                   closes the session early, so a slow page does not block the user's
  //                   other requests; wrapped so a failure here cannot break the exit
  // padID             the request id, or a fresh uniqid if the request never got one
  // padLogError       sends a message to the SAPI error log, tagged with that id
  // padSessionStart   starts the PHP session on demand, with the strict cookie flags

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

    // A declared name the application set to NULL or unset leaves the session: a logout
    // written as $user = NULL or unset ( $user ) kept the user, since only a set name was
    // written back.

    foreach ( $GLOBALS ['padSessionVars'] as $var )
      if ( isset ( $GLOBALS [$var] ) )
        $_SESSION [$var] = $GLOBALS [$var];
      else
        unset ( $_SESSION [$var] );

    session_write_close ();

  }

  // The one place a PHP session is started - for the names of $padSessionVars at the start
  // of the request, and on demand by whatever needs one later: a CSRF token, a flash
  // message. A session used to start only when $padSessionVars listed names, so a helper
  // that needed one had nowhere to keep its state.
  //
  // Strict mode: a session id the server never issued is refused and a fresh one made, so a
  // visitor cannot plant an id of their choosing. The cookie is kept from scripts and
  // cross-site requests, and travels only over https when the page did. Once headers have
  // gone out no cookie can be sent, so a session cannot start then and FALSE says so.
  //
  // $padSessionStarted tells padCloseSession that there is a session to write back and
  // close at the end of the request.

  function padSessionStart () {

    global $padSessionStarted;

    if ( session_status () === PHP_SESSION_ACTIVE ) {
      $padSessionStarted = TRUE;
      return TRUE;
    }

    if ( headers_sent () or session_status () === PHP_SESSION_DISABLED )
      return FALSE;

    ini_set ( 'session.use_strict_mode', '1' );

    session_set_cookie_params ( [
      'httponly' => TRUE,
      'samesite' => 'Lax',
      'secure'   => ( $_SERVER ['HTTPS'] ?? '' ) !== '' and ( $_SERVER ['HTTPS'] ?? '' ) !== 'off'
    ] );

    if ( ! session_start () )
      return FALSE;

    $padSessionStarted = TRUE;

    return TRUE;

  }

  // Whether the visitor brought a session along - the session cookie is there - without
  // starting one: a check that only reads the session has nothing to read without it, and
  // starting one would send a cookie for nothing.

  function padSessionExists () {

    return session_status () === PHP_SESSION_ACTIVE or isset ( $_COOKIE [ session_name () ] );

  }

  function padID () {

    global $padReqID;

    return $padReqID ?? uniqid ( '', TRUE );

  }

  function padLogError ( $error ) {

    error_log ( '[PAD] ' . padID () . ' ' . padMakeSafe ( $error ), 4 );

  }

?>
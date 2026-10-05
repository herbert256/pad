<?php

  // Flash messages: a message the page sets now - 'Thanks, your message was sent.' - and
  // the next page shows, once. The post-redirect-get pattern needs it: the page that took
  // the post redirects, and the page the browser lands on is the one that has to say what
  // happened.
  //
  // padFlash       keeps a message, with its type, in the session for the next request -
  //                the session starts on demand - and sets the padFlash cookie, the sign
  //                for the next request that there is something to take
  // padFlashTake   inits/flash.php: a request bringing the sign takes the messages out of
  //                the session for itself, so they are there for this request and gone
  //                from the session whether a page shows them or not
  // padFlashShow   {flash}: the messages of this request - the ones taken at the start,
  //                and any flashed while the page is being made - each shown once
  //
  // A message survives exactly one request after the one that flashed it, the redirect's
  // destination. The cookie keeps every other request from opening the session to look:
  // a page that never flashes costs nothing. Before this the demos passed a query flag,
  // ?contact&sent=1, which anyone could put in a link, and which stayed in the address bar.

  const padFlashCookie = 'padFlash';

  function padFlash ( $message, $type = 'info' ) {

    if ( ! padSessionStart () )
      return FALSE;

    $_SESSION ['padFlash'] [] = [ 'message' => (string) $message, 'type' => (string) $type ];

    padFlashCookie ( TRUE );

    return TRUE;

  }

  function padFlashTake () {

    global $padFlashNow;

    $padFlashNow = [];

    if ( ! isset ( $_COOKIE [padFlashCookie] ) )
      return;

    // The sign without the session it points into - expired, or the cookie outlived it:
    // nothing to take, and no session is started to find that out.

    if ( ! padSessionExists () or ! padSessionStart () ) {
      padFlashCookie ( FALSE );
      return;
    }

    $padFlashNow = is_array ( $_SESSION ['padFlash'] ?? NULL ) ? $_SESSION ['padFlash'] : [];

    unset ( $_SESSION ['padFlash'] );

    padFlashCookie ( FALSE );

  }

  // {flash} or {flash 'error'}: the messages of this request - of that type only, when
  // one is named - as rows with message and type, taken out so that no second {flash}
  // shows them again. Those flashed during this very request come along: a page that
  // flashes and renders itself shows them now, not on the next page. One of those this
  // {flash} does not show stays in the session, for the next request - a {flash 'error'}
  // took the success flashed beside it out of the session too, and nothing ever showed it.

  function padFlashShow ( $type = '' ) {

    global $padFlashNow;

    $taken = $padFlashNow ?? [];
    $fresh = [];

    if ( session_status () === PHP_SESSION_ACTIVE and is_array ( $_SESSION ['padFlash'] ?? NULL ) ) {
      $fresh = $_SESSION ['padFlash'];
      unset ( $_SESSION ['padFlash'] );
    }

    $show = [];
    $keep = [];
    $left = [];

    foreach ( [ $taken, $fresh ] as $set => $list )
      foreach ( $list as $one )
        if ( $type === '' or ( $one ['type'] ?? '' ) === $type )
          $show [] = [ 'message' => (string) ( $one ['message'] ?? '' ), 'type' => (string) ( $one ['type'] ?? '' ) ];
        elseif ( $set == 0 )
          $keep [] = $one;
        else
          $left [] = $one;

    $padFlashNow = $keep;

    if ( $left )
      $_SESSION ['padFlash'] = $left;
    elseif ( $fresh )
      padFlashCookie ( FALSE );

    return $show;

  }

  // The sign travels on the session cookie's path, the whole host, so a redirect into
  // another application of the site finds it. Gone again - an expired cookie - once taken.

  function padFlashCookie ( $set ) {

    if ( headers_sent () )
      return;

    if ( ( $GLOBALS ['padFlashSign'] ?? NULL ) === $set )
      return;

    if ( ! $set and ! isset ( $_COOKIE [padFlashCookie] ) and ! isset ( $GLOBALS ['padFlashSign'] ) )
      return;

    $GLOBALS ['padFlashSign'] = $set;

    setcookie ( padFlashCookie, $set ? '1' : '', [
      'expires'  => $set ? 0 : 1,
      'path'     => ini_get ( 'session.cookie_path' ) ?: '/',
      'httponly' => TRUE,
      'samesite' => 'Lax',
      'secure'   => ( $_SERVER ['HTTPS'] ?? '' ) !== '' and ( $_SERVER ['HTTPS'] ?? '' ) !== 'off'
    ] );

  }

?>

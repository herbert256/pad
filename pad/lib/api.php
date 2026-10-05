<?php

  // The navigation calls an application makes from its own PHP, rather than using
  // header()/exit which would bypass the engine's shutdown.
  //
  // padFastLink  serialises $vars into the PAD database links table under a random key
  //              and returns $padGoExt plus that key, so a large state set travels as a
  //              short URL (inits/fast.php is the matching unpacker, currently disabled)
  // padRedirect  builds an absolute cross-app URL from $padHost, appends the session and
  //              request ids when cookies are off (padAddIds) plus $vars, sends Location
  //              and ends the request with 302
  // padRestart   abandons the current page and reruns the request for $go by setting
  //              $padRestart / $padRestartVars, which start/restart.php acts on

  function padFastLink ( $padPage, $vars ) {

    global $padFastLink, $padSesID, $padReqID, $padGoExt;

    $vars ['padPage']  = $padPage;
    $vars ['padSesID'] = $padSesID;
    $vars ['padRefID'] = $padReqID;

    $fast = padRandomString ($padFastLink);

    padDb (
      "insert into links values('{0}','{1}')",
      [$fast, serialize($vars)]
    );

    return "$padGoExt$fast";

  }

  function padRedirect ( $go='', $vars=[], $app='' ) {

    global $padHost, $padApp, $padPage;

    if ( ! $app ) $app = $padApp;
    if ( ! $go  ) $go  = $padPage;

    $go = ( $go ) ? "$padHost$app/?$go" : "$padHost$app/";

    $go = padAddIds ( $go );

    foreach ( $vars as $padK => $padV )
      $go = padAddGet ( $go, $padK, $padV );

    padHeader ( "Location: $go" );

    padExit ( 302 );

  }

  function padRestart ( $go, $vars=[] ) {

    global $padRestart, $padRestartVars;

    $padRestart     = $go;
    $padRestartVars = $vars;

    return NULL;

  }

?>
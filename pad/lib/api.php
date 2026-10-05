<?php

  // The navigation calls an application makes from its own PHP, rather than using
  // header()/exit which would bypass the engine's shutdown.
  //
  // padRedirect  builds an absolute cross-app URL from $padHost, appends the session and
  //              request ids when cookies are off (padAddIds) plus $vars, sends Location
  //              and ends the request with 302
  // padRestart   abandons the current page and reruns the request for $go by setting
  //              $padRestart / $padRestartVars, which start/restart.php acts on

  function padRedirect ( $go='', $vars=[], $app='' ) {

    global $padHost, $padApp, $padPage, $padPageAsked, $padCleanUrls;

    // Back to this page is back to the name it was asked by - products/42, not the
    // products/[id] that answers it - and within the application, in the clean form when
    // $padCleanUrls says the links take it.

    if ( ! $go  ) $go  = ( $padPageAsked ?? '' ) !== '' ? $padPageAsked : $padPage;

    $clean = ( $padCleanUrls and ( ! $app or $app == $padApp ) );

    if ( ! $app ) $app = $padApp;

    $go = ( $go ) ? "$padHost$app/" . ( $clean ? '' : '?' ) . $go : "$padHost$app/";

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
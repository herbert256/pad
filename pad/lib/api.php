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

    // That name is the decoded path, and a segment a route bound may hold what an address
    // reads as its own - a&b, c#d, e%f - so each segment is encoded again: written as it
    // was, the browser came back to a, to c, to a broken escape.

    if ( ! $go  ) $go  = implode ( '/', array_map ( 'rawurlencode', explode ( '/', ( $padPageAsked ?? '' ) !== '' ? $padPageAsked : $padPage ) ) );

    // A page written the way a link writes it, ?about - {redirect} lets that form through
    // its check as a target of its own (start/redirect.php) - is that page: the ? is the one
    // the address gets anyway, where a second was put in front and the browser went to
    // ??about, a page that is not there.

    if ( str_starts_with ( (string) $go, '?' ) )
      $go = substr ( $go, 1 );

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

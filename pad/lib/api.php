<?php

  function padFastLink ( $padPage, $vars ) {

    global $padFastLink, $padSesID, $padReqID, $padHost, $padScript;

    $vars ['padPage']  = $padPage;
    $vars ['padSesID'] = $padSesID;
    $vars ['padRefID'] = $padReqID;

    $fast = padRandomString ($padFastLink);

    padDb (
      "insert into links values('{0}','{1}')",
      [$fast, serialize($vars)]
    );

    return "$padHost$padScript?$fast";

  }

  function padRedirect ( $go='', $vars=[], $app='' ) {

    global $padGoExt, $padHost, $padReqID, $padSesID, $padApp, $padPage;

    if ( ! $app ) $app = $padApp;
    if ( ! $go  ) $go  = $padPage;

    $go = ( $go ) ? "$padHost/$app/?$go" : "$padHost/$app/";

    $go = padAddGet ( $go, 'padSesID', $padSesID );
    $go = padAddGet ( $go, 'padReqID', $padReqID );

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
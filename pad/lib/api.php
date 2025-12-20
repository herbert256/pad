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

  function padRedirect ( $go, $vars=[] ) {

    global $padGoExt, $padHost, $padReqID, $padSesID;

    foreach ( $vars as $padK => $padV )
      $go .= "&$padK=" . urlencode($padV);

    if ( ! strpos($go, '://') )
      $go = $padGoExt . $go;

    $go = str_replace('SELF://', "$padHost/", $go);

    if  ( str_starts_with ( $go, $padHost ) ) {
      $go = padAddGet ( $go, 'padSesID', $padSesID );
      $go = padAddGet ( $go, 'padReqID', $padReqID );
    }

    padHeader ("Location: $go");

    padExit (302);

  }

  function padRestart ( $go, $vars=[] ) {

    global $padRestart, $padRestartVars;

    $padRestart     = $go;
    $padRestartVars = $vars;

    return NULL;

  }

?>

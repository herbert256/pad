<?php


  function padFastLink ( $padPage, $vars ) {
    
    global $padFastLink, $padSesID, $padReqID, $padHost;
  
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

    global $padHost, $padGoExt;

    foreach ( $vars as $padK => $padV ) 
      $go .= "&$padK=" . urlencode($padV);

    if ( ! strpos($go, '://') )
      $go = $padGoExt . $go;
  
    $go = str_replace('SELF://', "$padHost/", $go);

    if  ( str_starts_with ( $go, $padHost ) ) {
      $go = padAddGet ( $go, 'padSesID', $GLOBALS ['padSesID'] );
      $go = padAddGet ( $go, 'padReqID', $GLOBALS ['padReqID'] );
    }    

    padHeader ("Location: $go");
    
    padStop (302);

  }


  function padRestart ( $go, $vars=[] ) {

    $GLOBALS ['padRestart']     = $go;
    $GLOBALS ['padRestartVars'] = $vars;

    return NULL;

  }


?>
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


  function padPage ( $go ) {

    global $padHost, $padScript, $padStop;

    $parts = padExplode ($go, '://', 2);

    if ( count ($parts) == 2)
      $next = $go;
    else
      $next = "$padHost$padScript?padApp=$go";
  
    padHeader ("Location: $next");
    
    padStop (302);

  }


?>
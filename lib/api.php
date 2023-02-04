<?php


  function padFastLink ($app, $page, $vars ) {
    
    global $padFastLink, $PADSESSID, $PADREQID, $padHost, $pad;
  
    $vars ['app']       = $app;
    $vars ['page']      = $page;
    $vars ['PADSESSID'] = $PADSESSID;
    $vars ['PADREFID']  = $PADREQID;
    
    $fast = padRandomString ($padFastLink);
  
    padDb (
      "insert into links values('{0}','{1}')",
      [$fast, serialize($vars)]
    );
                        
    return $padHost . $pad . $fast;

  }


  function padPage ( $go ) {

    global $padHost, $padScript, $padStop;

    $parts = padExplode ($go, '://', 2);

    if ( count ($parts) == 2)
      $next = $go;
    else
      $next = "$padHost$padScript?app=$go";
  
    padHeader ("Location: $next");
    
    padStop (302);

  }


?>
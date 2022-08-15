<?php


  function pFast_link ($app, $page, $vars ) {
    
    global $padFast_link, $PADSESSID, $PADREQID, $padHost, $pad;
  
    $vars ['app']       = $app;
    $vars ['page']      = $page;
    $vars ['PADSESSID'] = $PADSESSID;
    $vars ['PADREFID']  = $PADREQID;
    
    $fast = pRandom_string ($padFast_link);
  
    pDb (
      "insert into links values('{0}','{1}')",
      [$fast, serialize($vars)]
    );
                        
    return $padHost . $pad . $fast;

  }


  function pGo ( $go ) {

    global $padHost, $padScript, $padStop;

    $padarts = pExplode ($go, '://', 2);

    if ( count ($padarts) == 2)
      $next = $go;
    else
      $next = "$padHost$padScript?app=$go";
  
    pHeader ("Location: $next");
    
    pStop (302);

  }


?>
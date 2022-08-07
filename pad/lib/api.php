<?php


  function pFast_link ($app, $page, $vars ) {
    
    global $pFast_link, $PADSESSID, $PADREQID, $pHost, $p;
  
    $vars ['app']       = $app;
    $vars ['page']      = $page;
    $vars ['PADSESSID'] = $PADSESSID;
    $vars ['PADREFID']  = $PADREQID;
    
    $fast = pRandom_string ($pFast_link);
  
    pDb (
      "insert into links values('{0}','{1}')",
      [$fast, serialize($vars)]
    );
                        
    return $pHost . $p . $fast;

  }


  function pGo ( $go ) {

    global $pHost, $pScript, $pStop;

    $parts = pExplode ($go, '://', 2);

    if ( count ($parts) == 2)
      $next = $go;
    else
      $next = "$pHost$pScript?app=$go";
  
    pHeader ("Location: $next");
    
    pStop (302);

  }


?>
<?php


  function pad_fast_link ($app, $page, $vars ) {
    
    global $pad_fast_link, $PADSESSID, $PADREQID, $pad_host, $pad;
  
    $vars ['app']       = $app;
    $vars ['page']      = $page;
    $vars ['PADSESSID'] = $PADSESSID;
    $vars ['PADREFID']  = $PADREQID;
    
    $fast = pad_random_string ($pad_fast_link);
  
    pad_db (
      "insert into links values('{0}','{1}')",
      [$fast, serialize($vars)]
    );
                        
    return $pad_host . $pad . $fast;

  }


  function pad_go ( $go ) {

    global $pad_host, $pad_script, $pad_stop;

    $parts = pad_explode ($go, '://', 2);

    if ( count ($parts) == 2)
      $next = $go;
    else
      $next = "$pad_host$pad_script?app=$go";
  
    pad_header ("Location: $next");
    
    pad_stop (302);

  }


?>
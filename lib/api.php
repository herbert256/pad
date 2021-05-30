<?php


  function padFastLink ($app, $page, $vars ) {
    
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


  function padLocation ( $location ) {

    $next = $GLOBALS['pad_location'] . $location;
  
    pad_header ("Location: $next");

    $pad_stop = '302';
    include PAD_HOME . 'exits/stop.php';

  }


  function padNext ( $next ) {
    $GLOBALS ['pad_next'] = $next;
  }
  

?>
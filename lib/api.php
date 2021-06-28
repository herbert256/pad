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

  function pad_go ( $go ) {

    global $pad_host, $pad_script, $app;

    if ( pad_file_exists ( PAD_APP . "pages/$go.php" ) or pad_file_exists ( PAD_APP . "pages/$go.html" ) )
      $next = "$pad_host$pad_script?app=$app&page=$go";
    else
      $next = $GLOBALS['pad_location'] . $go;
  
    pad_header ("Location: $next");
    flush();

    $pad_stop = '302';
    include PAD_HOME . 'exits/stop.php';

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
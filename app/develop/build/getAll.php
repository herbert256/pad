<?php

  deleteDir  ( DATA . '_xref'       ); 
  deleteDir  ( DATA . '_regression' );

  foreach ( padList ( 0 ) as $one ) {

    $item = $one ['item'];

    $curl = getPage ( $item, 1, 1 );
    $new  = $curl ['data'] ?? '';
    $new  = str_replace ( "\r\n", "\n", $new );

    $store  = DATA . "_regression/$item.html";
    padFileChkDir     ( $store );
    padFileChkFile    ( $store );
    file_put_contents ( $store, $new ) ;

    $store = DATA . "_regression/$item.txt";
    padFileChkDir     ( $store );
    padFileChkFile    ( $store );
    file_put_contents ( $store, 'ok' ) ;

  }

?>
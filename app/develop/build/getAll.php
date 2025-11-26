<?php

  deleteDir  ( APP . '_xref'       ); 
  deleteDir  ( APP . '_regression' );

  foreach ( padList ( 0 ) as $one ) {

    $item = $one ['item'];

    $curl = getPage ( $item, 1, 1 );
    $new  = $curl ['data'] ?? '';
    $new  = str_replace ( "\r\n", "\n", $new );

    $store  = APP . "_regression/$item.html";
    padFileChkDir     ( $store );
    padFileChkFile    ( $store );
    file_put_contents ( $store, $new ) ;

    $store = APP . "_regression/$item.txt";
    padFileChkDir     ( $store );
    padFileChkFile    ( $store );
    file_put_contents ( $store, 'ok' ) ;

  }

?>
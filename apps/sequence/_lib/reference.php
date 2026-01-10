<?php

  function getXref ( $dir ) {

    if ( ! $dir )
      return [];
   
    foreach ( padFiles ( DAT . "reference/sequence/$dir/" ) as $item ) {
      $item = str_replace ( '.txt', '', $item );   
      $files [$item] ['item']  = $item;
    }

    ksort ($files);

    return $files;

  }

?>
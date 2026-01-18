<?php

  function getXref ( $dir ) {

    if ( ! $dir )
      return [];

    $path = DAT . "reference/sequence/$dir/";

    if ( ! is_dir ( $path ) )
      return [];

    foreach ( padFiles ( $path ) as $item ) {
      $item = str_replace ( '.txt', '', $item );
      $files [$item] ['item']  = $item;
    }

    ksort ($files);

    return $files;

  }

?>
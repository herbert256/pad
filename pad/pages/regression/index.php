<?php

  $title = "Regression test";

  $ref   = get_reference_files ();

  $files = [];

  foreach ($ref as $item) {

    $files [$item] ['item'] = $item;

    $store_write = "regression/$item.html";
    $store_read  = DATA . $store_write;

    $curl = padComplete ('reference', $item);
  
    if     ( $curl ['result'] <> 200 )                              $status = $curl ['result'] ;
    elseif ( strrpos($store_write, 'random') )                      $status = 'random' ;
    elseif ( ! file_exists ($store_read) )                          $status = 'new';
    elseif ( padFileGetContents($store_read) == $curl ['data'] ) $status = 'ok';
    else                                                            $status = 'error';

    if ( $status == 'new' )
      padFilePutContents ($store_write, $curl ['data'] );

    $files [$item] ['status'] = $status;

  }

?>
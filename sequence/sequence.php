<?php

  if ( ! isset($pad_parms_tag ['walk']) )
    return include PAD_HOME . "sequence/build.php";

  if ( $pad_walk == 'start' ) {

    $pad_seq_store [$pad_lvl] = include PAD_HOME . "sequence/build.php";

    if ( ! is_array ($pad_seq_store[$pad_lvl]) or ! count ($pad_seq_store[$pad_lvl]) ) {
      $pad_walk = '';
      return $pad_seq_store [$pad_lvl];
    }

    $pad_seq_store_now = reset ( $pad_seq_store [$pad_lvl] ) ;

    $pad_walk = 'next';

  } else {

    $pad_seq_store_now = next ($pad_seq_store[$pad_lvl]);

    if ( $pad_seq_store_now === FALSE ) {
      $pad_walk = '';
      return NULL;
    }

  }

  return [ $pad_seq_store_now => $pad_seq_store_now ];

?>
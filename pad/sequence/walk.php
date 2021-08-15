<?php

  if ( $pad_walk == 'start' ) {

    $pad_seq_walk [$pad_lvl] = include 'build.php';

    if ( ! is_array ($pad_seq_walk[$pad_lvl]) or ! count ($pad_seq_walk[$pad_lvl]) ) {
      $pad_walk = '';
      return $pad_seq_walk [$pad_lvl];
    }

    $pad_seq_walk_now = reset ( $pad_seq_walk [$pad_lvl] ) ;

    $pad_walk = 'next';

  } else {

    $pad_seq_walk_now = next ($pad_seq_walk[$pad_lvl]);

    if ( $pad_seq_walk_now === FALSE ) {
      $pad_walk = '';
      return NULL;
    }

  }

  return [ $pad_seq_walk_now => $pad_seq_walk_now ];

?>
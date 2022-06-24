<?php

  $pad_seq_init = TRUE;

  if ( count ($pad_seq_for) and is_array(reset($pad_seq_for)) )
    foreach ( $pad_seq_for as $pad_k => $pad_v )
      $pad_seq_for [$pad_k] = reset($pad_v);

  foreach ( $pad_seq_for as $pad_seq_loop ) {

    if ( $pad_seq_random )
      $pad_seq_loop = $pad_seq_for [array_rand($pad_seq_for)] ;

    include 'go/one.php';

   $pad_seq_init = FALSE;

  }

?>
<?php

  $pad_seq_init = TRUE;

  foreach ( $pad_seq_for as $pad_seq_loop ) {

    if ( $pad_seq_random )
      $pad_seq_loop = $pad_seq_for [array_rand($pad_seq_for)] ;

    include 'go/one.php';

   $pad_seq_init = FALSE;

  }

?>
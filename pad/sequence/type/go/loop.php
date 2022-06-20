<?php

  $pad_go_init = TRUE;

  $pad_seq_go = $pad_seq_start;

  while ( $pad_seq_go <= $pad_seq_end ) {

    if ( $pad_seq_random )
      $pad_seq_loop = pad_seq_random ( $pad_seq_start, $pad_seq_end );
    else
      $pad_seq_loop = $pad_seq_go;

    if ( ! include 'one/one.php')
        break;

    $pad_go_init = FALSE;

    $pad_seq_go = $pad_seq_go + $pad_seq_inc;

  }

?>
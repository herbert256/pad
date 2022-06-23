<?php

  include PAD . "sequence/build/save.php";

  $pad_seq_init = TRUE;

  $pad_seq_go = $pad_seq_start;

  while ( $pad_seq_go <= $pad_seq_end ) {

    if ( ! $pad_seq_random )

      $pad_seq_loop = $pad_seq_go;

    elseif ( $pad_seq_inc == 1)

      $pad_seq_loop = pad_seq_random ( $pad_seq_start, $pad_seq_end );

    else {

      $pad_seq_inc_cnt = round ( (($pad_seq_end-$pad_seq_start)+1) / $pad_seq_inc );
      $pad_seq_inc_cnt = pad_seq_random ( 0, $pad_seq_inc_cnt );

      $pad_seq_loop = $pad_seq_start + ($pad_seq_inc_cnt*$pad_seq_inc);

    }

    if ( ! include 'go/one.php')
        break;

    $pad_seq_init = FALSE;

    $pad_seq_go = $pad_seq_go + $pad_seq_inc;

  }

?>
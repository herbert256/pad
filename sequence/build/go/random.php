<?php

  $pad_seq_rand = rand ( $pad_seq_init, $pad_seq_exit ); 

    if ( $pad_multiple and $pad_seq_rand % $pad_seq_multiple ) {

      $pad_seq_rand = ceil ( $pad_seq_rand / $pad_seq_multiple ) * $pad_seq_multiple;

      if ( $pad_seq_rand > $pad_seq_exit )
        $pad_seq_rand = $pad_seq_rand - $pad_seq_multiple;

      if ( $pad_seq_rand < $pad_seq_init )
        $pad_seq_rand = $pad_seq_rand + $pad_seq_multiple;

    }

  if ( $pad_seq_even and $pad_seq_rand % 2 )
    $pad_seq_rand++;

  if ( $pad_seq_odd and ! $pad_seq_rand % 2 )
    $pad_seq_rand++;

  if ( $pad_seq > $pad_seq_max)
    $pad_seq_rand = $pad_seq_rand - 2;

?>
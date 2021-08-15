<?php

  if ( gmp_prob_prime ($pad_seq_loop_idx) )
  	$pad_seq_prepare [0] = $pad_seq_loop_idx;
  else
    $pad_seq_prepare [0] = gmp_intval ( gmp_nextprime ($pad_seq_loop_idx) );

?>
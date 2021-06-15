<?php

  $pad_seq_rand = rand ( $pad_seq_loop_idx, $pad_seq_loop_end );

  if ( gmp_prob_prime ($pad_seq_rand) )
    return $pad_seq_rand;

  $pad_seq_rand = gmp_intval ( gmp_nextprime ($pad_seq_rand) );

  if ( $pad_seq_rand <= $pad_seq_loop_end )
    return $pad_seq_rand;
  elseif ( gmp_prob_prime ($pad_seq_loop_idx) )
    return $pad_seq_loop_idx;
  else
    return gmp_intval ( gmp_nextprime ($pad_seq_loop_idx) ); 

?>
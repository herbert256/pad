<?php

  $pad_seq_rand = rand ( $pad_seq_init, $pad_seq_exit );

  if ( gmp_prob_prime ($pad_seq_rand) )
    return $pad_seq_rand;

  $pad_seq_rand = gmp_intval ( gmp_nextprime ($pad_seq_rand) );

  if ( $pad_seq_rand <= $pad_seq_exit )
    return $pad_seq_rand;
  elseif ( gmp_prob_prime ($pad_seq_init) )
    return $pad_seq_init;
  else
    return gmp_intval ( gmp_nextprime ($pad_seq_init) ); 

?>
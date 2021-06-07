<?php

  $pad_seq_rand = rand ( $pad_seq_min, $pad_seq_max );

  if ( gmp_prob_prime ($pad_seq_rand) )
    return $pad_seq_rand;

  $pad_seq_rand = gmp_intval ( gmp_nextprime ($pad_seq_rand) );

  if ( $pad_seq_rand <= $pad_seq_max )
    return $pad_seq_rand;
  elseif ( gmp_prob_prime ($pad_seq_min) )
    return $pad_seq_min;
  else
    return gmp_intval ( gmp_nextprime ($pad_seq_min) ); 

?>
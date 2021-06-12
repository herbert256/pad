<?php

  $pad_seq_rand = rand ( $pad_seq_from, $pad_seq_to );

  if ( gmp_prob_prime ($pad_seq_rand) )
    return $pad_seq_rand;

  $pad_seq_rand = gmp_intval ( gmp_nextprime ($pad_seq_rand) );

  if ( $pad_seq_rand <= $pad_seq_to )
    return $pad_seq_rand;
  elseif ( gmp_prob_prime ($pad_seq_from) )
    return $pad_seq_from;
  else
    return gmp_intval ( gmp_nextprime ($pad_seq_from) ); 

?>
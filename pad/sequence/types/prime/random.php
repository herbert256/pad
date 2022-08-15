<?php

  if ( gmp_prob_prime ($padSeq_rand) )
    return $padSeq_rand;

  $padSeq_rand = gmp_intval ( gmp_nextprime ($padSeq_rand) );

  if ( $padSeq_rand <= $padSeq_max )
    return $padSeq_rand;
  elseif ( gmp_prob_prime ($padSeq_min ) )
    return $padSeq_min;
  else
    return gmp_intval ( gmp_nextprime ( $padSeq_min ) ); 

?>
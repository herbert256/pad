<?php

  if ( gmp_prob_prime ($pSeq_rand) )
    return $pSeq_rand;

  $pSeq_rand = gmp_intval ( gmp_nextprime ($pSeq_rand) );

  if ( $pSeq_rand <= $pSeq_max )
    return $pSeq_rand;
  elseif ( gmp_prob_prime ($pSeq_min ) )
    return $pSeq_min;
  else
    return gmp_intval ( gmp_nextprime ( $pSeq_min ) ); 

?>
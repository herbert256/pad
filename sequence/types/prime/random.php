<?php

  if ( gmp_prob_prime ($padSeqRand) )
    return $padSeqRand;

  $padSeqRand = gmp_intval ( gmp_nextprime ($padSeqRand) );

  if ( $padSeqRand <= $padSeqTo )
    return $padSeqRand;
  elseif ( gmp_prob_prime ($padSeqFrom ) )
    return $padSeqFrom;
  else
    return gmp_intval ( gmp_nextprime ( $padSeqFrom ) ); 

?>
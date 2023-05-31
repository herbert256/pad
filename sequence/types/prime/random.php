<?php

  if ( gmp_prob_prime ($padSeqRand) )
    return $padSeqRand;

  $padSeqRand = gmp_intval ( gmp_nextprime ($padSeqRand) );

  if ( $padSeqRand <= $padSeqMax )
    return $padSeqRand;
  elseif ( gmp_prob_prime ($padSeqMin ) )
    return $padSeqMin;
  else
    return gmp_intval ( gmp_nextprime ( $padSeqMin ) ); 

?>
<?php

  if ( ! gmp_prob_prime ( $pSeq_loop ) )
    return false;

  if ( $pSeq_loop < 11 )
    return false;

   $pReverse = pSeq_reverse($pSeq_loop);

  if ( gmp_prob_prime ( $pReverse ) ) 
    return TRUE;
  else
    return false;

?>
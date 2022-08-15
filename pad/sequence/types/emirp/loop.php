<?php

  if ( ! gmp_prob_prime ( $padSeq_loop ) )
    return false;

  if ( $padSeq_loop < 11 )
    return false;

   $padReverse = padSeq_reverse($padSeq_loop);

  if ( gmp_prob_prime ( $padReverse ) ) 
    return TRUE;
  else
    return false;

?>
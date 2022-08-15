<?php

  if ( ! gmp_prob_prime ( $padSeqLoop ) )
    return false;

  if ( $padSeqLoop < 11 )
    return false;

   $padReverse = padSeq_reverse($padSeqLoop);

  if ( gmp_prob_prime ( $padReverse ) ) 
    return TRUE;
  else
    return false;

?>
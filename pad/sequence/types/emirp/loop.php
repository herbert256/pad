<?php

  if ( ! gmp_prob_prime ( $pad_seq_now ) )
    return false;

  if ( $pad_seq_now < 11 )
    return false;

   $pad_reverse = pad_reverse($pad_seq_now);

  if ( gmp_prob_prime ( $pad_reverse ) ) 
    return TRUE;
  else
    return false;

?>
<?php

  if ( ! gmp_prob_prime ( $pad_sequence ) )
    return false;

  if ( $pad_sequence < 11 )
    return false;

   $pad_reverse = pad_reverse($pad_sequence);

  if ( gmp_prob_prime ( $pad_reverse ) ) 
    return TRUE;
  else
    return false;

?>
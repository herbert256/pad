<?php

  if ( ! gmp_prob_prime ( $pad_seq_loop ) )
    return false;

  if ( $pad_seq_loop < 11 )
    return false;

   $pad_reverse = pad_seq_reverse($pad_seq_loop);

  if ( gmp_prob_prime ( $pad_reverse ) ) 
    return TRUE;
  else
    return false;

?>
<?php

  function padSeqBoolPrime ( $n ) {

    if ( ! function_exists ( 'gmp_prob_prime' ) )
      return TRUE;
 
    if ( gmp_prob_prime ($n) )
      return TRUE;
    else
      return FALSE;

  }

?>
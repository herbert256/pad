<?php

  function padSeqBoolPrime ( $n ) {

    if ( gmp_prob_prime ($n) )
      return TRUE;
    else
      return FALSE;

  }

?>
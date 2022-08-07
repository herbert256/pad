<?php

  function pSequence_bool_prime ( $n ) {

    if ( gmp_prob_prime ($n) )
      return TRUE;
    else
      return FALSE;

  }

?>
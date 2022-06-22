<?php

  function pad_sequence_bool_prime ( $n ) {

    if ( gmp_prob_prime ($n) )
      return TRUE;
    else
      return FALSE;

  }

?>
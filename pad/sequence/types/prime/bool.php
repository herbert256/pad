<?php

  function pad_seq_now_bool_prime ( $n ) {

    if ( gmp_prob_prime ($n) )
      return TRUE;
    else
      return FALSE;

  }

?>
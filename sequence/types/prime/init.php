<?php

  if ( gmp_prob_prime ($pad_seq_init) )
  	return $pad_seq_init;
  else
    return gmp_intval ( gmp_nextprime ($pad_seq_init) );

?>
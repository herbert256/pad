<?php

  if ( gmp_prob_prime ($pad_seq_min) )
  	return $pad_seq_min;
  else
    return gmp_intval ( gmp_nextprime ($pad_seq_min) );

?>
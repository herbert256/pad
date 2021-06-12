<?php

  if ( gmp_prob_prime ($pad_seq_from) )
  	return $pad_seq_from;
  else
    return gmp_intval ( gmp_nextprime ($pad_seq_from) );

?>
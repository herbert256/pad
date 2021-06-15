<?php

  if ( gmp_prob_prime ($pad_seq_loop_idx) )
  	return $pad_seq_loop_idx;
  else
    return gmp_intval ( gmp_nextprime ($pad_seq_loop_idx) );

?>
<?php

  if ( gmp_prob_prime ($pad_seq_loop) )
    return $pad_seq_loop;
  else
    return gmp_nextprime ($pad_seq_loop);

?>
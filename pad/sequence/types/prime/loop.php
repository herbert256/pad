<?php

  if ( gmp_prob_prime ($pad_seq_now) )
    return $pad_seq_now;
  else
    return gmp_nextprime ($pad_seq_now);

?>
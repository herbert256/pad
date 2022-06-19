<?php

  if ( gmp_prob_prime ($pad_sequence) )
    return $pad_sequence;
  else
    return gmp_nextprime ($pad_sequence);

?>
<?php

  if ( gmp_prob_prime ($pSeq_loop) )
    return $pSeq_loop;
  else
    return gmp_nextprime ($pSeq_loop);

?>
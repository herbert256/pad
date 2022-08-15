<?php

  if ( gmp_prob_prime ($padSeq_loop) )
    return $padSeq_loop;
  else
    return gmp_nextprime ($padSeq_loop);

?>
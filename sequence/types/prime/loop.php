<?php

  if ( gmp_prob_prime ($padSeqLoop) )
    return $padSeqLoop;
  else
    return gmp_nextprime ($padSeqLoop);

?>
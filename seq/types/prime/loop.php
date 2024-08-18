<?php

  include_once 'bool.php';

  return padSeqBoolPrime ($padSeqLoop);
  
  if ( gmp_prob_prime ($padSeqLoop) )
    return $padSeqLoop;
  else
    return intval (gmp_nextprime ($padSeqLoop));

?>
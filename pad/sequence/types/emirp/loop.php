<?php

  if ( ! function_exists ( 'gmp_prob_prime' ) )
    return TRUE;
  
  if ( ! gmp_prob_prime ( $pqLoop ) )
    return false;

  if ( $pqLoop < 11 )
    return false;

  $padReverse = (int) padTypeReverse($pqLoop);

  if ( gmp_prob_prime ( $padReverse ) ) 
    return TRUE;
  else
    return false;

?>
<?php

  // Evaluates a play built by the 'position' strategy - build='function' on a type whose terms
  // are the values its bool.php test accepts: the candidate-th of those values, FALSE when
  // the candidate is no position.

  if ( ! pqBoolWhole ( $pqLoop ) )
    return FALSE;

  return pqPosition ( $pqSeq, $pqParm, (int) ( $pqLoop + 0 ) );

?>

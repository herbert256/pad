<?php

  // Evaluates a play built by the 'bool' strategy: returns the type's own predicate
  // pqBoolXxx() applied to the candidate, so plays/plays.php gets a plain TRUE or FALSE.

  return ( 'pqBool' . ucfirst($pqSeq) ) ( $pqLoop, $pqParm );

?>
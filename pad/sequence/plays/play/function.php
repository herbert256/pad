<?php

  // Evaluates a play built by the 'function' strategy: returns the type's own generator
  // pqXxx() applied to the candidate, so plays/plays.php gets that sequence's term for it.

  return ( 'pq' . ucfirst($pqSeq) ) ( $pqLoop );

?>
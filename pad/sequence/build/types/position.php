<?php

  // Build strategy 'position': what build='function' runs for a type whose terms are the values
  // its bool.php test accepts. The loop iterator walks the positions and build/one.php asks
  // pqPosition () for the value at each - from=, to= and increment= count terms, as they do
  // for every type built by position.

  include PQ . 'build/types/type/loop.php';

?>

<?php

  // Build strategy 'given': the terms were handed in as a literal array by the caller -
  // sequence/actions/set.php puts them in $pqFixed when an action is applied to an array
  // rather than to a named sequence. Nothing to generate, just walk them.

  include PQ . 'build/types/type/fixed.php';

?>
<?php

  // Build strategy 'bool': the type has a predicate pqBoolXxx(), so the from/to range is
  // walked and every value the predicate accepts becomes a term. Nothing to set up - the
  // loop iterator drives it and build/one.php makes the call.

  include PQ . 'build/types/type/loop.php';

?>
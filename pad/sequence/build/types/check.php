<?php

  // Build strategy 'check': membership testing, the strategy pqBuild() picks for {keep},
  // {remove} and {flag}. The loop iterator walks the range and build/one.php settles each
  // candidate through build/check.php.

  include PQ . 'build/types/type/loop.php';

?>
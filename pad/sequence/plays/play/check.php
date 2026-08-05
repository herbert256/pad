<?php

  // Evaluates a play built by the 'check' strategy - the one pqBuild() picks for {keep},
  // {remove} and {flag}. Defers to build/check.php, which answers whether the candidate is
  // a member of the sequence, so plays/plays.php gets a plain TRUE or FALSE.

  return include PQ . "build/check.php";

?>
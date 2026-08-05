<?php

  // Loop build for prime, the strategy pqBuild() picks for this type: each candidate is put
  // to pqBoolPrime() and kept when it is prime - build/one.php reads TRUE as "keep the loop
  // value" and FALSE as "skip it" - so the type filters the range rather than indexing it.
  //
  // bool.php is included here as well as by build/include.php, because a play reaches this
  // file without the type's helpers having been loaded.

  include_once PT . "prime/bool.php";

  return pqBoolPrime ($pqLoop);

?>
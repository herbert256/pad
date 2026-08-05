<?php

  // pull: - returns a stored sequence by name from $pqStore, the sequence subsystem's store of
  // sequences that {keep}/{make} have put aside.

  global $pqStore;

  return $pqStore [$name];

?>
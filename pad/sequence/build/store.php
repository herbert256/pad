<?php

  // Supplies the sequence parameter from a stored sequence, one of its terms per result.
  //
  // When the parm names a stored sequence rather than a value ($pqParmStore, set by
  // build/vars.php), build/one.php includes this as an expression per candidate; it
  // returns the stored term lining up with the number of results produced so far, so
  // parameter and result advance in step.
  //
  // A store shorter than the sequence has no term for the results past its end, and a term
  // cannot be made without its parameter: NULL comes back and build/one.php ends the run
  // there - {sequence add='s', rows=6} over a store of three gives three terms. It read past
  // the end of the store and ended the request on the undefined key.

  $pqStoreIdx = count ( $pqResult );

  return $pqStore [$pqParmStore] [$pqStoreIdx] ?? NULL;

?>
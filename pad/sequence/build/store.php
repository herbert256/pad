<?php

  // Supplies the sequence parameter from a stored sequence, one of its terms per result.
  //
  // When the parm names a stored sequence rather than a value ($pqParmStore, set by
  // build/vars.php), build/one.php includes this as an expression per candidate; it
  // returns the stored term lining up with the number of results produced so far, so
  // parameter and result advance in step.

  $pqStoreIdx = count ( $pqResult );

  return $pqStore [$pqParmStore] [$pqStoreIdx];

?>
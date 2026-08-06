<?php

  // onlyNow='seq' - keeps the values of the current sequence that are absent from the
  // named store, discarding the store's own extra values. onlyStore is the mirror image
  // and difference is the two together. Without a parameter, or with one naming a store
  // that was never pushed, nothing happens - comparing against a store that is not there
  // ended the request.

  if ( $pqActionParm and isset ( $pqStore [$pqActionParm] ) )
    $pqResult = array_diff ( $pqResult, $pqStore [$pqActionParm] );

?>
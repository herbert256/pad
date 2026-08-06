<?php

  // onlyStore='seq' - the mirror image of onlyNow: replaces the sequence with the values
  // of the named store that are absent from it, so the result comes out of the store, not
  // out of the sequence being pulled. Without a parameter, or with one naming a store that
  // was never pushed, nothing happens - comparing against a store that is not there ended
  // the request.

  if ( $pqActionParm and isset ( $pqStore [$pqActionParm] ) )
    $pqResult = array_diff ( $pqStore [$pqActionParm], $pqResult );

?>
<?php

  // onlyStore='seq' - the mirror image of onlyNow: replaces the sequence with the values
  // of the named store that are absent from it, so the result comes out of the store, not
  // out of the sequence being pulled. Without a parameter nothing happens.

  if ( $pqActionParm )
    $pqResult = array_diff ( $pqStore [$pqActionParm], $pqResult );

?>
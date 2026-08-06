<?php

  // append='seq' - adds every value of each named stored sequence to the end of the
  // current one, keeping duplicates and renumbering. prepend does the same at the front.
  //
  // A name that is not a store is skipped rather than walked as one.

  foreach ( $pqActionList as $pqAppendKey )
    if ( isset ( $pqStore [$pqAppendKey] ) )
      foreach ($pqStore [$pqAppendKey] as $pqAppendValue)
        $pqResult [] = $pqAppendValue;

?>
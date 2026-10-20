<?php

  // Last extra step: keeps the rows of a store this run wrote in $padSeqData, beside the bare
  // terms in $pqStore, so a later {pull}/{resume} of that name can chain the columns back in
  // (extra/chain.php). $padSeqData survives the run; the $pq* globals do not.
  //
  // A store a shift or pop took from keeps the rows of the entries left in it, in the order
  // they are left - the rows it was pulled with, at the store positions the actions did not
  // take ($pqStoreTakenAt). It got the rows of the part taken, and the next pull gave its
  // terms the columns of other terms; and a shift in a {pull:a} pair, which writes no store
  // of its own, filed its rows under the store pushed last, which another store's pull then
  // read.
  //
  // A store this run wrote ($pqStored, under $pqStoredAs) keeps the rows the run shows - unless
  // it is the store taken from, whose rows are the remainder's.
  //
  // The rows left are read from the rows the run pulled, $pqPulledRows, as the store's values
  // are rebuilt from $pqPulled (actions/types/shift.php).

  if ( $pqStoreUpdated ) {

    if ( $pqPulledRows !== NULL and isset ( $padSeqData [$pqPull] ) ) {

      $pqChainLeft = [];

      foreach ( $pqPulled as $pqChainAt => $pqChainValue )
        if ( ! isset ( $pqStoreTakenAt [$pqChainAt] ) )
          $pqChainLeft [] = $pqPulledRows [$pqChainAt] ?? [];

      $padSeqData [$pqPull] = $pqChainLeft;

    }

  }

  if ( $pqStored and ! ( $pqStoreUpdated and $pqStoredAs == $pqPull ) )
    $padSeqData [$pqStoredAs] = $padData [$pad];

?>

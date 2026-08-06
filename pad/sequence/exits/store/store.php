<?php

  // Store stage of the wind-down: settles the store name, checks that name is free, and
  // writes the terms into $pqStore under it, so a later {pull}/{resume} can pick them up.
  //
  // A tag pair ({sequence}...{/sequence}) is there to iterate its terms, not to leave them
  // behind, so it is skipped unless push= or name= asked for the terms to be kept. Naming a
  // run is how the single-tag form stores it, so the pair form answers to the name too -
  // without that, closing the tag silently cost the store and a later {sequence myName}
  // fell through to the default counter.

  if ( $padPair [$pad] and ! $pqPush and ! $pqNameGiven )
    return;

  include PQ . 'exits/store/last.php';
  include PQ . 'exits/store/check.php';
  include PQ . 'exits/store/set.php';

?>
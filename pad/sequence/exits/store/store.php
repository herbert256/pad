<?php

  // Store stage of the wind-down: settles the store name, checks that name is free, and
  // writes the terms into $pqStore under it, so a later {pull}/{resume} can pick them up.
  //
  // A tag pair ({sequence}...{/sequence}) is there to iterate its terms, not to leave them
  // behind, so it is skipped unless push= was asked for explicitly.

  if ( $padPair [$pad] and ! $pqPush )
    return;

  include PQ . 'exits/store/last.php';
  include PQ . 'exits/store/check.php';
  include PQ . 'exits/store/set.php';

?>
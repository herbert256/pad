<?php

  // Marker file, never included: its existence keeps 'store' from being the name of a store
  // - see exits/store/check.php. Bare it passes as a sequence word; given a value it is no
  // option - pqOption () in lib/sequence.php - and the strict check names it as unread.
  //
  // Nothing in the subsystem reads a store= parameter; naming a store is done with push=
  // and pull=, and exits/store/ does the writing into $pqStore. So this entry only reserves
  // the name.

  return TRUE;

?>

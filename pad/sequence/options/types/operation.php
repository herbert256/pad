<?php

  // Marker file, never included: its existence keeps 'operation' from being the name of a store
  // - see exits/store/check.php. Bare it passes as a sequence word; given a value it is no
  // option - pqOption () in lib/sequence.php - and the strict check names it as unread.
  //
  // Nothing in the subsystem reads an operation= parameter - the play operations are named
  // directly, as make, keep, remove or flag - so this entry only reserves the name.

  return TRUE;

?>

<?php

  // Marker file, never included: its existence keeps 'single' from being the name of a store
  // - see exits/store/check.php. Bare it passes as a sequence word; given a value it is no
  // option - pqOption () in lib/sequence.php - and the strict check names it as unread.
  //
  // Nothing in the subsystem reads a single= parameter; what narrows a run down to one
  // value is sole= (a range of one candidate) or one of the aggregate actions flagged in
  // actions/single/. So this entry only reserves the name.

  return TRUE;

?>

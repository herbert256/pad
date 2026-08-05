<?php

  // pop, pop=N - takes the last N entries off the stored sequence being pulled and hands
  // them back as the result. The work is in shift.php, which branches on $pqAction.
  // Destructive on the store; use last for a plain non-destructive selection.

  include PQ . 'actions/types/shift.php';

?>
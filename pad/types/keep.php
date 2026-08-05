<?php

  // Type handler for the sequence keep operation ({keep:name}): delegates to the sequence
  // subsystem's entry point, which stores the sequence for later pulls.

  return include PQ . 'start/types/keep.php';

?>
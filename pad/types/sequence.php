<?php

  // Type handler for a sequence ({sequence:fibonacci}, or a bare tag naming a type in
  // PQ/types/): delegates to the sequence subsystem's entry point.

  return include PQ . 'start/types/sequence.php';

?>
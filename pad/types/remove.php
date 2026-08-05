<?php

  // Type handler for the sequence remove operation ({remove:mySeq}): delegates to the sequence
  // subsystem's entry point, which drops the sequence from the store.

  return include PQ . 'start/types/remove.php';

?>
<?php

  // Type handler for the sequence keep operation ({keep:prime}, {mySeq:keep prime}):
  // delegates to the sequence subsystem's entry point, which keeps only the values the named
  // sequence type accepts - {remove:prime} is its mirror image. Nothing is stored by it.

  return include PQ . 'start/types/keep.php';

?>
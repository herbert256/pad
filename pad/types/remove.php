<?php

  // Type handler for the sequence remove operation ({remove:prime}, {mySeq:remove prime}):
  // delegates to the sequence subsystem's entry point, which drops the values the named
  // sequence type accepts and keeps the rest - the mirror image of {keep:prime}. No store
  // is dropped or changed by it.

  return include PQ . 'start/types/remove.php';

?>
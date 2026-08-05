<?php

  // Type handler for a stored sequence ({pull:mySeq}, or a bare tag whose name is in $pqStore):
  // delegates to the sequence subsystem's entry point, which pulls the stored values.

  return include PQ . 'start/types/pull.php';

?>
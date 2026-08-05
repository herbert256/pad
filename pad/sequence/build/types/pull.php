<?php

  // Build strategy 'pull': the terms come from a sequence stored earlier under a name or
  // pushed by a previous tag. Lifts $pqStore[$pqPull] into $pqFixed and hands it to the
  // fixed iterator, so from/to/plays/actions apply to the stored terms as usual.

  $pqFixed = $pqStore [$pqPull];

  include PQ . 'build/types/type/fixed.php';

?>
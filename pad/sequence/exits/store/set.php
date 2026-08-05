<?php

  // Writes the store: $pqStore[$padLastPush] gets the terms this run returns, or - when a
  // pop/shift action consumed from a pulled store ($pqStoreUpdated) - what is left of that
  // store, since there the returned terms are the slice taken off rather than the remainder.
  //
  // Sets $pqStored, which tells exits/extra/set.php to keep the rows for chaining. Also
  // included straight from sequence/sequence/resume.php.

  if ( $pqStoreUpdated ) $pqStore [$padLastPush] = array_values ( $pqStore [$pqPull] );
  else                   $pqStore [$padLastPush] = array_values ( $pqResult );

  $pqStored = TRUE;

?>
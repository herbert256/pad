<?php

  if ( $pqStoreUpdated ) $pqStore [$padLastPush] = array_values ( $pqStore [$pqPull] );
  else                   $pqStore [$padLastPush] = array_values ( $pqResult );

  $pqStored = TRUE;

?>
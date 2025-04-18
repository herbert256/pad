<?php

  if ( $pqStoreUpdated ) $pqStore [$padLastPush] = $pqStore [$pqPull];
  else                   $pqStore [$padLastPush] = array_values ( $pqResult );

?>
<?php

  if ( $padSeqStoreUpdated ) $padSeqStore [$padLastPush] = $padSeqStore [$padSeqPull];
  else                       $padSeqStore [$padLastPush] = array_values ( $padSeqResult );

?>
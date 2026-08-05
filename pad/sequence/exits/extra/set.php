<?php

  // Last extra step: when this run wrote a store ($pqStored, or $pqStoreUpdated after a
  // pop/shift), keeps the finished rows in $padSeqData[$padLastPush] alongside the bare terms
  // in $pqStore, so a later {pull}/{resume} of that name can chain the columns back in
  // (extra/chain.php). $padSeqData survives the run; the $pq* globals do not.

  if ( $pqStored or $pqStoreUpdated )
    $padSeqData [$padLastPush] = $padData [$pad];

?>
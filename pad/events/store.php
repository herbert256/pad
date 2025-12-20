<?php

  global $padInfoTrace, $padInfoTraceStore;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceStore )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'store', $padName [$pad], $padStoreData );

?>

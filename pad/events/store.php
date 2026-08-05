<?php

  // Fires from tags/_go/store.php right after a {data} / {content} / {bool} block has been
  // written into its global store, and traces the name it was stored under together with the
  // stored value when $padInfoTraceStore is on.

  global $padInfoTrace, $padInfoTraceStore;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceStore )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'store', $padName [$pad], $padStoreData );

?>
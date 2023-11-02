<?php

  if ( ! $GLOBALS ['padTraceStore'] )
    return;

  padTrace ( 'store', $padName [$pad], $padStoreData ); 

  padTraceFile ( 'store', 'txt', $padStoreData );

?>
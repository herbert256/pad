<?php

  if ( ! $GLOBALS ['padTraceItems'] ['store'] )
    return;

  padTrace ( 'store', $padName [$pad], $padStoreData ); 

  padTraceFile ( 'store', 'txt', $padStoreData );

?>
<?php

  if ( ! $GLOBALS ['padTraceTypes'] ['store'] )
    return;

  padTrace ( 'store', $padName [$pad], $padStoreData ); 

  padTraceFile ( 'store', 'txt', $padStoreData );

?>
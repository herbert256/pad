<?php

  if ( ! $GLOBALS ['padTraceTypes'] ['store'] )
    return;

  padTrace ( 'store', $padName [$pad], $padStoreData ); 

  if ( $GLOBALS ['padTraceTree'] )
    padTraceFile ( 'store', 'txt', $padStoreData );

?>
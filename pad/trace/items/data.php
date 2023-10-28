<?php

  if ( ! $padTraceTypes ['data'] )
    return;

  if ( $padTraceTree and ! padIsDefaultData ( $padData [$pad] ) )
    padTraceFile ( 'data', 'json', $padData [$pad] );

?>
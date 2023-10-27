<?php

  if ( ! $padTraceTypes ['data'] )
    return;

  if ( ! padIsDefaultData ( $padData [$pad] ) )
    padTraceFile ( 'data', 'json',   $padData [$pad] );

?>
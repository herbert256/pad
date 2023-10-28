<?php

  if ( ! $padTraceTypes ['true'] )
    return;

  padTrace ( 'level', 'true',  $padTrue [$pad] ); 

  if ( $padTraceTree and $padTrue [$pad] )
    padTraceFile ( 'true', 'pad', $padTrue [$pad] );

?>
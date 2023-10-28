<?php

  if ( ! $padTraceTypes ['base'] )
    return;

  padTrace ( 'level', 'base',  $padBase [$pad] ); 

  if ( $padTraceTree and $padBase [$pad] )
    padTraceFile ( 'base', 'pad', $padBase [$pad] );

?>
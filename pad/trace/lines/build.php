<?php

  if ( $padTraceTypes ['build'] )
    padTrace ( 'build', 'info', $padBase [$pad] ); 

  if ( $padTraceTypes ['base'] )
    padTraceFile ( 'base', 'pad', $padBase [$pad] );

?>
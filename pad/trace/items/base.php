<?php

  if ( ! $padTraceTypes ['base'] )
    return;

  padTrace ( 'level', 'base',  $padBase [$pad] ); 

  if ( $padBase [$pad] and strlen ( $padBase [$pad] ) > 50 )
    padTraceFile ( 'base', 'pad', $padBase [$pad] );

?>
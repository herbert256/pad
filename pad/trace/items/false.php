<?php

  if ( ! $padTraceTypes ['false'] )
    return;

  padTrace ( 'level', 'false',  $padFalse [$pad] ); 

  if ( $padTraceTree and $padFalse [$pad] )
    padTraceFile ( 'false', 'pad', $padFalse [$pad] );

?>
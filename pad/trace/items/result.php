<?php

  if ( ! $padTraceTypes ['result'] )
    return;

  padTrace ( 'level', 'result', $padResult [$pad] ); 

  if ( $padTraceTree and $padResult [$pad] )
    padTraceFile ( 'result', 'pad', $padResult [$pad] );

?>
<?php

  if ( ! $padTraceTypes ['content'] )
    return;

  padTrace ( 'level', 'content', $padTrue [$pad] ); 

  if ( $padTraceTree and $padTrue [$pad]  )
    padTraceFile ( 'content', 'pad', $padTrue [$pad] );

?>
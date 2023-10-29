<?php

  if ( ! $padTraceTypes ['content'] )
    return;

  padTrace ( 'level', 'content', $padTrue [$pad] ); 

  if ( $padTrue [$pad] and strlen ( $padTrue [$pad] ) > 50 )
    padTraceFile ( 'content', 'pad', $padTrue [$pad] );

?>
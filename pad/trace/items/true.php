<?php

  if ( ! $padTraceTypes ['true'] )
    return;

  padTrace ( 'level', 'true',  $padTrue [$pad] ); 

  if ( $padTrue [$pad] and strlen ( $padTrue [$pad] ) > 50 )
    padTraceFile ( 'true', 'pad', $padTrue [$pad] );

?>
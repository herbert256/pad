<?php

  if ( ! $padTraceResult )
    return;

  padTrace ( 'level', 'result', $padResult [$pad] ); 

  if ( $padResult [$pad] and strlen ( $padResult [$pad] ) > 50 )
    padTraceFile ( 'result', 'pad', $padResult [$pad] );

?>
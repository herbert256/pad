<?php

  if ( ! $padTraceTrue )
    return;

  if ( $padTraceNoDouble and $padTraceContent and $padTrue [$pad] == $padTraceStartContent [$pad] )
    return;

  padTrace ( 'level', 'true',  $padTrue [$pad] ); 

?>
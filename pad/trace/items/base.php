<?php

  if ( ! $padTraceLevelBase )
    return;

  if ( $padTraceNoDouble and $padTraceContent and $padTrue [$pad] == $padTraceStartContent [$pad] )
    return;

  padTrace ( 'level', 'base',  $padBase [$pad] ); 

?>
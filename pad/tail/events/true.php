<?php

  if ( ! $padTraceTrue )
    return;

  if ( !$padTraceDouble and $padTraceContent and $padBase [$pad] == $padPadStart [$pad] )
    return;

  padTrace ( 'level', 'true',  $padBase [$pad] ); 

?>
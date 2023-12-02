<?php

  if ( ! $padTraceLevelBase )
    return;

  if ( !$padTraceDouble and $padTraceContent and $padBase [$pad] == $padPadStart [$pad] )
    return;

  padTrace ( 'level', 'base',  $padBase [$pad] ); 

?>
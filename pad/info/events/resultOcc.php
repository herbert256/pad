<?php

  if ( ! $padTraceResultOcc )
    return;

  if ( !$padTraceDouble and $padTraceContent and $padBase [$pad] == $padPad [$pad] )
    return;

  padTrace ( 'occur', 'occ-result', $padPad [$pad] );

?>
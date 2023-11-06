<?php

  if ( ! $padTraceResultOcc )
    return;

  if ( $padTraceNoDouble and $padTraceContent and $padTraceStartContent [$pad] == $padPad [$pad] )
    return;

  padTrace ( 'occur', 'occ-result', $padPad [$pad] );

?>
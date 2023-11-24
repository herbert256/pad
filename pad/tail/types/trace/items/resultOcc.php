<?php

  if ( ! $padTraceResultOcc )
    return;

  if ( $padTraceNoDouble and $padTraceContent and $padPadStart [$pad] == $padPad [$pad] )
    return;

  padTrace ( 'occur', 'occ-result', $padPad [$pad] );

?>
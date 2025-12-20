<?php

  global $padInfoTrace;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceResultOcc )
    return;

  if ( !$padInfoTraceDouble and $padInfoTraceContent and $padBase [$pad] == $padOut [$pad] )
    return;

 if ( $padInfoTrace ) padInfoTrace ( 'occur', 'occ-result', $padOut [$pad] );

?>

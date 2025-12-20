<?php

  if ( ! $GLOBALS ['padInfoTrace'] )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceResultOcc )
    return;

  if ( !$padInfoTraceDouble and $padInfoTraceContent and $padBase [$pad] == $padOut [$pad] )
    return;

 if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'occur', 'occ-result', $padOut [$pad] );

?>

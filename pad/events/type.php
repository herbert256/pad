<?php

  if ( ! $padInfoTrace or ! $padInfoTraceResultLvl )
    return;

  if ( $padInfoTraceDouble and $padInfoTraceContent and $padBase [$pad] == $padResult [$pad] )
    return;

  padInfoTrace ( 'level', 'result',  $padResult [$pad] );

?>

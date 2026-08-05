<?php

  // Fires from level/go.php once the tag's type handler has run and the level flags are set,
  // and traces the level's result, $padResult [$pad], when $padInfoTraceResultLvl is on.
  // events/resultOcc.php does the same for a single occurrence.

  if ( ! $padInfoTrace or ! $padInfoTraceResultLvl )
    return;

  if ( $padInfoTraceDouble and $padInfoTraceContent and $padBase [$pad] == $padResult [$pad] )
    return;

  padInfoTrace ( 'level', 'result',  $padResult [$pad] );

?>
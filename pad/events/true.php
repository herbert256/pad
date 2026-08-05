<?php

  // Fires from the trace report's level block (info/types/trace/level/info.php, reached from
  // events/levelStart.php) and logs the tag's true-branch content, $padBase [$pad], as a
  // 'level true' entry when $padInfoTraceTrue is on. Its counterpart is events/false.php.

  if ( ! $padInfoTrace or ! $padInfoTraceTrue )
    return;

  if ( !$padInfoTraceDouble and $padInfoTraceContent )
    padInfoTrace ( 'level', 'true',  $padBase [$pad] );

?>
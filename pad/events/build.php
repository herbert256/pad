<?php

  // Fires once from pad/build/build.php, right after the page has been assembled into
  // $padBase[$pad] (_lib includes + _inits.pad + @page@ + _exits.pad) and before the
  // outermost occurrence loop starts.
  //
  // Logs the assembled source when $padInfoTraceBuild, then opens the trace for the root
  // level and emits its info dump - the build-time counterpart of events/levelStart.php.

  global $padInfoTrace;

  if ( $padInfoTrace )
    if ($padInfoTraceBuild )
      padInfoTrace ( 'build', 'info', $padBase [$pad] );

  if ( $padInfoTrace ) include PAD . 'info/types/trace/level/start.php';
  if ( $padInfoTrace ) include PAD . 'info/types/trace/level/info.php';

?>
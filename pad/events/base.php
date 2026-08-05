<?php

  // One of the six sub-events of the per-level trace dump (pad/info/types/trace/level/
  // info.php); logs the level's raw, still unprocessed base template text.
  //
  // Needs $padInfoTraceLevelBase and $padInfoTraceContent, and stays quiet when
  // $padInfoTraceDouble is on, since content is then logged by events/type.php instead.

  global $padInfoTrace, $padInfoTraceLevelBase;

  if ( $padInfoTrace and $padInfoTraceLevelBase )
    if ( ! $padInfoTraceDouble and $padInfoTraceContent )
      padInfoTrace ( 'level', 'base',  $padBase [$pad] );

?>
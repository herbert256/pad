<?php

  // Sub-event of the per-level trace dump (pad/info/types/trace/level/info.php); logs
  // $padFalse, the half of the level's base that sits after @else@ and is rendered only
  // when the tag misses.
  //
  // Needs $padInfoTraceFalse and $padInfoTraceContent; like its twin events/true.php it
  // stays quiet when $padInfoTraceDouble is on.

  if ( ! $padInfoTrace or ! $padInfoTraceFalse )
    return;

  if ( !$padInfoTraceDouble and $padInfoTraceContent )
    padInfoTrace ( 'level', 'false', $padFalse );

?>
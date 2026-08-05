<?php

  // First sub-event of the per-level trace dump (pad/info/types/trace/level/info.php);
  // reports $padData[$pad], the data set the level is about to iterate.
  //
  // Needs $padInfoTraceDataLvl, and unless $padInfoTraceDefault is set it skips levels
  // carrying only PAD's default single-occurrence data, which would otherwise swamp the
  // trace. Besides the trace line it writes the data as data.json in the level's trace
  // directory.

  global $padInfoTrace;

  if ( ! $padInfoTrace )
    return;

  if ( $padInfoTraceDataLvl ) {

    if ( ! $padInfoTrace or ! $padInfoTraceDefault and padIsDefaultData ( $padData [$pad] ) )
      return;

   if ( $padInfoTrace ) padInfoTrace ( 'level', 'data', $padData [$pad] );

    padInfoTraceWrite ( $pad, "data.json", $padData [$pad], 'file' );

  }

?>
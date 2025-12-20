<?php

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

<?php

  if ( $padTraceDataLvl ) {

    if ( $padTraceNoDefault and padIsDefaultData ( $padData [$pad] ) )
      return;
  
    padTrace ( 'level', 'data', $padData [$pad] );

    padTraceWrite ( $pad, "data.json", $padData [$pad], 'file' );
  
  }

?>
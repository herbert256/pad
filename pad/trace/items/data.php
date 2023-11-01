<?php

  if ( ! $padTraceItems ['data'] )
    return;

  padTrace ( 'level', 'data', $padData [$pad] );

  if ( padIsDefaultData ( $padData [$pad] ) )
    return;

  if ( count ( $padData [$pad] ) )
    padTraceFile ( 'data', 'json', $padData [$pad] );

?>
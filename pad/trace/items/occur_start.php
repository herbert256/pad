<?php

  for ( $padI = $pad; $padI; $padI-- )
    $padTraceChilds[$padI]++;

  if ( ! $padTraceTypes ['occur'] )
    return;

  padTrace ( 'occur', 'start', $padBase [$pad] );

  if ( $padTraceTypes ['base'] and $padBase [$pad] and strlen ( $padBase [$pad] ) > 50 )
    padTraceFile ( 'occ-base', 'pad', $padBase [$pad] );   

  if ( $padTraceTypes ['data'] ) {

    if ( padIsDefaultData ( $padBase [$pad] ) )
      return;

    if ( count ( $padData [$pad] ) )
      padTraceFile ( 'occ-data', 'pad', $padCurrent [$pad] );   

  }

?>
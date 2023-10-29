<?php

  for ( $padI = $pad; $padI; $padI-- )
    $padTraceChilds[$padI]++;

  if ( ! $padTraceTypes ['occur'] )
    return;

  if ( $padWalk [$pad] <> 'next' and padIsDefaultData ( $padData [$pad] ) )
    return;

  padTrace ( 'occur', 'start', $padBase [$pad] );

  if ( $padTraceTypes ['base'] and $padBase [$pad] and strlen ( $padBase [$pad] ) > 50 )
    padTraceFile ( 'base', 'pad', $padBase [$pad] );   

  if ( $padTraceTypes ['data'] ) {

    if ( padIsDefaultData ( $padBase [$pad] ) )
      return;

    if ( count ( $padData [$pad] ) )
      padTraceFile ( 'data', 'pad', $padCurrent [$pad] );   

  }

?>
<?php

  for ( $padI = $pad; $padI; $padI-- )
    $padTraceLevelChilds[$padI]++;

  $padTraceOccurChilds [$pad] [$padOccur[$pad]] = 0;;

  if ( $pad > 0)
    for ( $padI = $pad-1; $padI; $padI-- )
      $padTraceOccurChilds[$padI][$padOccur[$padI]]++;

  padTrace ( 'occur', 'start', $padBase [$pad] );

  if ( $padTraceBase and $padBase [$pad] and strlen ( $padBase [$pad] ) > 50 )
    padTraceFile ( 'occ-base', 'pad', $padBase [$pad] );   

  if ( $padTraceData ) {

    if ( padIsDefaultData ( $padBase [$pad] ) )
      return;

    if ( count ( $padData [$pad] ) )
      padTraceFile ( 'occ-data', 'pad', $padCurrent [$pad] );   

  }

?>
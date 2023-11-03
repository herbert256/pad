<?php

  $padTraceOccurChilds [$pad] [$padOccur[$pad]] = 0;;

  for ( $padI = $pad; $padI; $padI-- )
    $padTraceLevelChilds [$padI] ++;

  if ( $pad > 0)
    for ( $padI = $pad-1; $padI; $padI-- )
      if ( $padOccur [$padI] )
        $padTraceOccurChilds[$padI][$padOccur[$padI]]++;

  padTrace ( 'occur', 'start', $padBase [$pad] );

  if ( $padTraceData ) {

    if ( padIsDefaultData ( $padBase [$pad] ) )
      return;

    if ( count ( $padData [$pad] ) )
      padTrace ( 'occur', 'data', $padCurrent [$pad] );   

  }

?>
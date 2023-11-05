<?php

  $padTraceOccurChilds [$pad] [$padOccur[$pad]] = 0;;

  for ( $padI = $pad; $padI >= $padTraceGo; $padI-- )
    $padTraceLevelChilds [$padI] ++;

  if ( $pad > 0)
    for ( $padI = $pad-1; $padI >= $padTraceGo; $padI-- )
      if ( $padOccur [$padI] )
        $padTraceOccurChilds[$padI][$padOccur[$padI]]++;

  padTraceSet ( 'occur', 'start' );

  if ( $padTraceStartOcc )
    padTrace ( 'occur', 'start', $padBase [$pad] );

  if ( $padTraceDataOcc ) {

    if ( $padTraceNoDefault and ! count ( $padCurrent [$pad] ) )
      return;
    
    padTrace ( 'occur', 'data', $padCurrent [$pad] );   
  
  }

?>
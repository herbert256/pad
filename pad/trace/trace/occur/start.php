<?php

  padTrace ( 'occur', 'start', $padBase [$pad] );

  for ( $padI = $pad; $padI >= $padTraceGo; $padI-- ) {

    if ( ! isset ( $padTraceLevelChilds [$padI] ) ) 
      $padTraceLevelChilds [$padI] = 0;

    $padTraceLevelChilds [$padI] ++;

    $padJ = $padOccur [$padI] ?? 0;

    if ( $padJ) { 

       if ( ! isset ($padTraceOccurChilds [$padI]         ) ) $padTraceOccurChilds [$padI] [$padJ] = 0;
       if ( ! isset ($padTraceOccurChilds [$padI] [$padJ] ) ) $padTraceOccurChilds [$padI] [$padJ] = 0;

       $padTraceOccurChilds [$padI] [$padJ] ++;

    }

  }
 
  if ( $padTraceDataOcc ) {

    if ( $padTraceNoDefault and ! count ( $padCurrent [$pad] ) )
      return;
    
    padTrace ( 'occur', 'occ-data', $padCurrent [$pad] );   
  
  }

?>
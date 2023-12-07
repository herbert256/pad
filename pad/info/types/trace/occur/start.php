<?php

  if ( $padTraceStartEndOcc )
    padTrace ( 'occur', 'start' );

  for ( $padI = $pad; $padI >= 0; $padI-- ) {

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

    if ( ! $padTraceDefault and ! count ( $padCurrent [$pad] ) )
      return;
    
    padTrace ( 'occur', 'occ-data', $padCurrent [$pad] );   

    $padJ = $padOccur [$pad];

    padTraceWrite ( $pad, "data-$padJ.json", $padCurrent [$pad], 'file' );
  
  }

?>
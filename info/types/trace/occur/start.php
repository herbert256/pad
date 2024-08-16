<?php

  if ( $padInfTraceStartEndOcc )
   $GLOBALS ['padInfo']( 'occur', 'start' );

  for ( $padI = $pad; $padI >= 0; $padI-- ) {

    if ( ! isset ( $padInfTraceLevelChilds [$padI] ) ) 
      $padInfTraceLevelChilds [$padI] = 0;

    $padInfTraceLevelChilds [$padI] ++;

    $padJ = $padOccur [$padI] ?? 0;

    if ( $padJ) { 

       if ( ! isset ($padInfTraceOccurChilds [$padI]         ) ) $padInfTraceOccurChilds [$padI] [$padJ] = 0;
       if ( ! isset ($padInfTraceOccurChilds [$padI] [$padJ] ) ) $padInfTraceOccurChilds [$padI] [$padJ] = 0;

       $padInfTraceOccurChilds [$padI] [$padJ] ++;

    }

  }
 
  if ( $padInfTraceDataOcc ) {

    if ( ! $padInfTraceDefault and ! count ( $padCurrent [$pad] ) )
      return;
    
   $GLOBALS ['padInfo']( 'occur', 'occ-data', $padCurrent [$pad] );   

    $padJ = $padOccur [$pad];

    padTraceWrite ( $pad, "data-$padJ.json", $padCurrent [$pad], 'file' );
  
  }

?>
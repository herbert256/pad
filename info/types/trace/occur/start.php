<?php

  if ( $padInfoTraceStartEndOcc )
   padInfoTrace ( 'occur', 'start' );

  for ( $padI = $pad; $padI >= 0; $padI-- ) {

    if ( ! isset ( $padInfoTraceLevelChilds [$padI] ) ) 
      $padInfoTraceLevelChilds [$padI] = 0;

    $padInfoTraceLevelChilds [$padI] ++;

    $padJ = $padOccur [$padI] ?? 0;

    if ( $padJ) { 

       if ( ! isset ($padInfoTraceOccurChilds [$padI]         ) ) $padInfoTraceOccurChilds [$padI] [$padJ] = 0;
       if ( ! isset ($padInfoTraceOccurChilds [$padI] [$padJ] ) ) $padInfoTraceOccurChilds [$padI] [$padJ] = 0;

       $padInfoTraceOccurChilds [$padI] [$padJ] ++;

    }

  }
 
  if ( $padInfoTraceDataOcc ) {

    if ( ! $padInfoTraceDefault and ! count ( $padCurrent [$pad] ) )
      return;
    
   padInfoTrace ( 'occur', 'occ-data', $padCurrent [$pad] );   

    $padJ = $padOccur [$pad];

    padInfoTraceWrite ( $pad, "data-$padJ.json", $padCurrent [$pad], 'file' );
  
  }

?>
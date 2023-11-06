<?php

  $padTraceOccurChilds  [$pad] [$padOccur[$pad]] = 0;;
  $padTraceOccurWritten [$pad] [$padOccur[$pad]] = FALSE;

  for ( $padI = $pad; $padI >= $padTraceGo; $padI-- )
    $padTraceLevelChilds [$padI] ++;

  if ( $pad > 0)
    for ( $padI = $pad-1; $padI >= $padTraceGo; $padI-- )
      if ( $padOccur [$padI] )
        $padTraceOccurChilds[$padI][$padOccur[$padI]]++;

  padTraceSet ( 'occur', 'start' );

  if ( $padTraceStartOcc ) 
    padTrace ( 'occur', 'start', $padBase [$pad] );

  padTrace ( 'occur', 'dummy', $padOccur[$pad] );
  padTrace ( 'abc', 'dummy', $padOccur[$pad] );

  if ( $padTraceDataOcc ) {

    if ( $padTraceNoDefault and ! count ( $padCurrent [$pad] ) )
      return;
    
    padTrace ( 'occur', 'occ-data', $padCurrent [$pad] );   
  
  }

?>
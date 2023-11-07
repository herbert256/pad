<?php

  padTraceXmlInitsOpened ();

  if ( $padTraceXmlWhere [$pad] <> 'occurs' ) 
    padTraceShowOccurs ();


  $padTraceOccurChilds  [$pad] [$padOccur[$pad]] = 0;
  $padTraceOccurWritten [$pad] [$padOccur[$pad]] = FALSE;

  for ( $padI = $pad; $padI >= $padTraceGo; $padI-- ) {

    $padTraceLevelChilds [$padI] ++;

    if ( $padOccur [$padI] )
       $padTraceOccurChilds[$padI][$padOccur[$padI]]++;

  }

  $padTraceXmlWhere [$pad] = 'occurs';

  padTraceSet ( 'occur', 'start' );

  padTrace ( 'occur', 'dummy', 'This will be in every occur - 1'); 
  padTrace ( 'dummy', 'dummy', 'This will be in every occur - 2'); 

  if ( $padTraceStartOcc ) 
    padTrace ( 'occur', 'start', $padBase [$pad] );

  if ( $padTraceDataOcc ) {

    if ( $padTraceNoDefault and ! count ( $padCurrent [$pad] ) )
      return;
    
    padTrace ( 'occur', 'occ-data', $padCurrent [$pad] );   
  
  }

?>
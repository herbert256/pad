<?php

  $padTraceOccurHasDir  [$pad] [$padOccur[$pad]] = FALSE; 
  $padTraceOccurDirName [$pad] [$padOccur[$pad]] = $padTraceDir; 

  for ( $padI = $pad; $padI; $padI-- )
    $padTraceChilds[$padI]++;

  $padTraceOccurs[$pad]++;

  padTrace ( 'occur', 'start', $padBase [$pad] );

 if ( ! $padTraceOccur )
    return;
 
  if ( $padTraceBase and $padBase [$pad] and strlen ( $padBase [$pad] ) > 50 )
    padTraceFile ( 'occ-base', 'pad', $padBase [$pad] );   

  if ( $padTraceData ) {

    if ( padIsDefaultData ( $padBase [$pad] ) )
      return;

    if ( count ( $padData [$pad] ) )
      padTraceFile ( 'occ-data', 'pad', $padCurrent [$pad] );   

  }

?>
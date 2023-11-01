<?php

  for ( $padI = $pad; $padI; $padI-- )
    $padTraceChilds[$padI]++;

  $padTraceOccurs[$pad]++;

  padTrace ( 'occur', 'start', $padBase [$pad] );

  $padTraceOccurHasDir  [$pad] [$padOccur[$pad]] = FALSE; 
  $padTraceOccurDirName [$pad] [$padOccur[$pad]] = $padTraceDir; 

 if ( ! $padTraceItems ['occur'] )
    return;
 
  if ( $padTraceItems ['base'] and $padBase [$pad] and strlen ( $padBase [$pad] ) > 50 )
    padTraceFile ( 'occ-base', 'pad', $padBase [$pad] );   

  if ( $padTraceItems ['data'] ) {

    if ( padIsDefaultData ( $padBase [$pad] ) )
      return;

    if ( count ( $padData [$pad] ) )
      padTraceFile ( 'occ-data', 'pad', $padCurrent [$pad] );   

  }

?>
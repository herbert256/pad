<?php


  if ( ! $padPair [$pad] or $padWalk [$pad] == 'start' ) {

    $padTrcCnt++;

    $padTraceDirNow = $padTraceDir . '/trace-' . $padTrcCnt;

  }


  if ( ! $padPair [$pad] ) {

    padTrace ( $padTraceDirNow );

    return NULL;

  }


  if ( $padWalk [$pad] == 'start' ) {  

    $padWalk [$pad] = 'end';

    $padBackupadTrace [$pad] ['trace'] = $padTrace;
    $padBackupadTrace [$pad] ['level'] = $padLevelDir [$pad-1] ?? '';
    $padBackupadTrace [$pad] ['occur'] = $padOccurDir [$pad-1] ?? '';

    $padLevelDir [$pad]   = $padTraceDirNow; 
    $padOccurDir [$pad]   = $padTraceDirNow;
    $padLevelDir [$pad-1] = $padTraceDirNow; 
    $padOccurDir [$pad-1] = $padTraceDirNow;

    padTrace ( $padLevelDir [$pad] . "/trace-start" );

    $padTrace = TRUE;
  
  } else {

    padTrace ( $padLevelDir [$pad] . "/trace-end" );

    $padTrace             = $padBackupadTrace [$pad] ['trace'];
    $padLevelDir [$pad-1] = $padBackupadTrace [$pad] ['level'];
    $padOccurDir [$pad-1] = $padBackupadTrace [$pad] ['occur'];
 
  } 

  return TRUE;

     
?>
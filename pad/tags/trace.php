<?php

  if ( $padWalk [$pad] == 'start' ) {  

    $padWalk [$pad] = 'end';

    $padBackupadTrace [$pad] = [];
    $padBackupadTrace [$pad] ['trace'] = $padTrace;
    $padBackupadTrace [$pad] ['level'] = $padLevelDir [$pad-1] ?? '';
    $padBackupadTrace [$pad] ['occur'] = $padOccurDir [$pad-1] ?? '';

    $padLevelDir [$pad-1] = $padTraceDir . '/trace-' . $padCnt; 
    $padOccurDir [$pad-1] = $padLevelDir [$pad-1];

    padTraceAll ( $padLevelDir [$pad] . "/start" );
  
  } else {

    padTraceAll ( $padLevelDir [$pad] . "/end" );

    $padTrace           = $padBackupadTrace [$pad] ['trace'];
    $padLevelDir [$pad-1] = $padBackupadTrace [$pad] ['level'];
    $padOccurDir [$pad-1] = $padBackupadTrace [$pad] ['occur'];
 
  } 

  return TRUE;
     
?>
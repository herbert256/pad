<?php
      
  $durationSet = padTagParm ('duration', $padOpt [$pad] [1]);

  if ( $padWalk [$pad] == 'start' ) {
    $duration [$durationSet] ['item'] = $durationSet;
    $duration [$durationSet] ['time1'] = hrtime(TRUE);
    $duration [$durationSet] ['time2'] = microtime(TRUE);
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $duration [$durationSet] ['time1'] = hrtime(TRUE) - $duration [$durationSet] ['time1'];
  $duration [$durationSet] ['time2'] = microtime(TRUE) - $duration [$durationSet] ['time2'];
  
  return TRUE;
  
?>
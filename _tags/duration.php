<?php
      
  $padDurationSet = padTagParm ('duration', $padOpt [$pad] [1]);

  if ( $padWalk [$pad] == 'start' ) {
    $padDuration [$padDurationSet] ['item'] = $padDurationSet;
    $padDuration [$padDurationSet] ['time'] = hrtime(TRUE);
    $padWalk [$pad] = 'end';
    return TRUE;
  }

  $padDuration [$padDurationSet] ['time'] = hrtime(TRUE) - $padDuration [$padDurationSet] ['time'];
  
  return TRUE;
  
?>
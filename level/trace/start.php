<?php

  if ( ! $padTrace )
    return;

  $padLevelDir [$pad] = $padOccurDir [$pad-1] . 
    '/tags/' . $padCnt . '-' . $padTag [$pad] . '-' . $padType [$pad] ;

  $padOccurDir [$pad] = $padLevelDir [$pad];
  
  padTraceFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

  padFilePutContents ( $padTraceDir . "/levels.txt",
    padFixedLenghtLeft  ($pad, 4) . '     ' .
    padFixedLenghtRight ($padType [$pad] . '/' . $padTag [$pad], 25) . ' ' .
    padFixedLenghtRight ($padPrm [$pad] [0], 60),
    1
  );

  padFilePutContents ( $padLevelDir [$pad] . "/start.json",      padTraceGetLevel($pad)  );
  padFilePutContents ( $padLevelDir [$pad] . "/app-start.json",  $padFapp );
  padFilePutContents ( $padLevelDir [$pad] . "/pad-start.json",  $padFpad ); 

?>
<?php

  if ( ! $padTrace )
    return;

  $padLevelDir [$pad] = $padOccurDir [$pad-1] . '/tag-' . $padCnt . '-' . $padTag [$pad] ;
  $padOccurDir [$pad] = $padLevelDir [$pad];
  
  padTraceFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

  padFilePutContents ( $padLevelDir [$pad] . "/start.json",      padTraceGetLevel($pad)  );
  padFilePutContents ( $padLevelDir [$pad] . "/app-start.json",  $padFapp );
  padFilePutContents ( $padLevelDir [$pad] . "/pad-start.json",  $padFpad ); 

?>
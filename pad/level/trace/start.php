<?php

  if ( ! $padTrace )
    return;

  $padLevelDir [$pad] = $padOccurDir [$pad-1] . '/tag-' . $padCnt . '-' . $padTag [$pad] ;
  $padOccurDir [$pad] = $padLevelDir [$pad];
  
  pTraceFields ( $padFphp, $padFlvl, $padFapp, $padFcfg, $padFpad, $padFids );

  pFile_put_contents ( $padLevelDir [$pad] . "/start.json",      pTraceGetLevel($pad)  );
  pFile_put_contents ( $padLevelDir [$pad] . "/app-start.json",  $padFapp );
  pFile_put_contents ( $padLevelDir [$pad] . "/pad-start.json",  $padFpad ); 

?>
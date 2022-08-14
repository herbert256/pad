<?php

  if ( ! $pTrace )
    return;

  $pLevelDir [$p] = $pOccurDir [$p-1] . '/tag-' . $pCnt . '-' . $pTag [$p] ;
  $pOccurDir [$p] = $pLevelDir [$p];
  
  pFields ( $pFphp, $pFlvl, $pFapp, $pFcfg, $pFpad, $pFids );

  pFile_put_contents ( $pLevelDir [$p] . "/start.json",      pTraceGetLevel($p)  );
  pFile_put_contents ( $pLevelDir [$p] . "/app-start.json",  $pFapp );
  pFile_put_contents ( $pLevelDir [$p] . "/pad-start.json",  $pFpad ); 

?>
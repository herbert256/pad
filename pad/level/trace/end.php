<?php

  if ( ! $pTrace )
    return;

  pFields ( $pFphp, $pFlvl, $pFapp, $pFcfg, $pFpad, $pFids );

  pFile_put_contents ( $pLevelDir [$p] . "/end.json",      pTraceGetLevel ($p) );
  pFile_put_contents ( $pLevelDir [$p] . "/app-end.json",  $pFapp );
  pFile_put_contents ( $pLevelDir [$p] . "/pad-end.json",  $pFpad ); 
  pFile_put_contents ( $pLevelDir [$p] . "/result.html", $pResult [$p] );
  
?>
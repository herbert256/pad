<?php
  
  if ( ! $pTrace )
    return;

  pFile_put_contents ( $pLevelDir [$p] . "/level.json", pTraceGetVars() ); 
  pFile_put_contents ( $pLevelDir [$p] . "/base.html",  $pBase [$p]     );
  pFile_put_contents ( $pLevelDir [$p] . "/data.json",  $pData [$p]     );
  
?>
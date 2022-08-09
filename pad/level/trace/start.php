<?php

  if ( ! $pTrace )
    return;

  $pLevelDir [$p] = $pOccurDir [$p-1] . '/tag-' . $pCnt . '-' . $pTag [$p] ;
  $pOccurDir [$p] = $pLevelDir [$p];
  
  pFile_put_contents ( $pLevelDir [$p] . "/app-start.json", pTrace_get_app_vars ()  );
  pFile_put_contents ( $pLevelDir [$p] . "/pad-start.json", pTraceGetVars() ); 

?>
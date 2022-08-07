<?php

  if ( ! $pTrace )
    return;

  $pLevelDir = $pOccurDir;

  if ( $p > 1)
    $pLevelDir[$p] .= '/tag-' . $pCnt . '-' . $[$p] ['tag'] ;


  pFile_put_contents ( $pLevelDir . "/level.json",     $[$p] );  
  pFile_put_contents ( $pLevelDir . "/pad-start.json", pTrace_get_pVars ()  );
  pFile_put_contents ( $pLevelDir . "/app-start.json", pTrace_get_app_vars ()  );
  pFile_put_contents ( $pLevelDir . "/html-base.html", $pBase[$p]        );
  pFile_put_contents ( $pLevelDir . "/data.json",      $pData[$p]        );

?>
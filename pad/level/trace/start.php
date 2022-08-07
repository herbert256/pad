<?php

  if ( ! $pTrace )
    return;

  if ( $p > 1)
    $pLevelDir[$p] = $pLevelDir[$p-1] . '/tag-' . $pCnt . '-' . $[$p] ['tag'] ;

  pFile_put_contents ( $pLevelDir[$p] . "/level.json",     $[$p] );  
  pFile_put_contents ( $pLevelDir[$p] . "/pad-start.json", pTrace_get_pVars ()  );
  pFile_put_contents ( $pLevelDir[$p] . "/app-start.json", pTrace_get_app_vars ()  );
  pFile_put_contents ( $pLevelDir[$p] . "/html-base.html", $pBase[$p]        );
  pFile_put_contents ( $pLevelDir[$p] . "/data.json",      $pData[$p]        );

?>
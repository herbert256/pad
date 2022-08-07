<?php

  if ( $p <= 1 )
    return;

  $pOccurDir [$p]  [$p] = $pLevelDir [$p]  [$p] . '/occur-' . $pOccur [$p];

  pFile_put_contents ( $pOccurDir [$p]  [$p] . "/data.json",      $pCurrent [$p]       );
  pFile_put_contents ( $pOccurDir [$p]  [$p] . "/pad.json",       pTrace_get_pVars () );
  pFile_put_contents ( $pOccurDir [$p]  [$p] . "/app.json",       pTrace_get_app_vars () );
  pFile_put_contents ( $pOccurDir [$p]  [$p] . "/html-base.html", $pBase [$p]          );
  
?>
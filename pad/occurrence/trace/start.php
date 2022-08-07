<?php

  if ( $p <= 1 )
    return;

  $pOccurDir [$p] = $pLevelDir [$p] . '/occur-' . $pOccur[$p];

  pFile_put_contents ( $pOccurDir [$p] . "/data.json",      $pCurrent[$p]       );
  pFile_put_contents ( $pOccurDir [$p] . "/pad.json",       pTrace_get_pVars () );
  pFile_put_contents ( $pOccurDir [$p] . "/app.json",       pTrace_get_app_vars () );
  pFile_put_contents ( $pOccurDir [$p] . "/html-base.html", $pBase[$p]          );
  
?>
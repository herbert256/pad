<?php

  $pOccurDir = pParmslevel_dir'];

  if ( $p > 1 and $pTag[$p]<> 'trace' )
    $pOccurDir .= '/occur-' . $pOccur[$p];

  $pOccurDir[$p]= $pOccurDir ;

  if ( $p <= 1 )
    return;

  pFile_put_contents ( $OccurDir [$] . "/data.json",      $pCurrent[$p]       );
  pFile_put_contents ( $OccurDir [$] . "/pad.json",       pTrace_get_pVars () );
  pFile_put_contents ( $OccurDir [$] . "/app.json",       pTrace_get_app_vars () );
  pFile_put_contents ( $OccurDir [$] . "/html-base.html", $pBase[$p]          );
  
?>
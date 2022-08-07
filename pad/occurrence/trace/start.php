<?php

  $pOccurDir = pParmslevel_dir'];

  if ( $pad > 1 and $pTag <> 'trace' )
    $pOccurDir .= '/occur-' . $pOccur[$p];

  $pOccurDir[$p]= $pOccurDir ;

  if ( $pad <= 1 )
    return;

  pFile_put_contents ( "$pOccurDir/data.json",      $pCurrent[$p]       );
  pFile_put_contents ( "$pOccurDir/pad.json",       pTrace_get_pad_vars () );
  pFile_put_contents ( "$pOccurDir/app.json",       pTrace_get_app_vars () );
  pFile_put_contents ( "$pOccurDir/html-base.html", $pBase[$p]          );
  
?>
<?php

  $pOccur_dir = $pParms [$pad] ['level_dir'];

  if ( $pad > 1 and $pTag <> 'trace' )
    $pOccur_dir .= '/occur-' . $pOccur [$pad];

  $pParms [$pad] ['occur_dir'] = $pOccur_dir ;

  if ( $pad <= 1 )
    return;

  pFile_put_contents ( "$pOccur_dir/data.json",      $pCurrent [$pad]       );
  pFile_put_contents ( "$pOccur_dir/pad.json",       pTrace_get_pad_vars () );
  pFile_put_contents ( "$pOccur_dir/app.json",       pTrace_get_app_vars () );
  pFile_put_contents ( "$pOccur_dir/html-base.html", $pBase [$pad]          );
  
?>
<?php

  if ( ! $pTrace_level )
    return;

  $pLevel_dir = $pOccur_dir;

  if ( $pad > 1)
    $pLevel_dir .= '/tag-' . $pLvl_cnt . '-' . $pParms[$pad] ['tag'] ;

  $pOccur_dir = $pLevel_dir;

  $pParms [$pad] ['level_dir'] = $pLevel_dir;
  $pParms [$pad] ['occur_dir'] = $pOccur_dir;

  pFile_put_contents ( "$pLevel_dir/level.json",     $pParms [$pad] );  
  pFile_put_contents ( "$pLevel_dir/pad-start.json", pTrace_get_pad_vars ()  );
  pFile_put_contents ( "$pLevel_dir/app-start.json", pTrace_get_app_vars ()  );
  pFile_put_contents ( "$pLevel_dir/html-base.html", $pBase[$pad]        );
  pFile_put_contents ( "$pLevel_dir/data.json",      $pData[$pad]        );

?>
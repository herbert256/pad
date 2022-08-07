<?php

  if ( ! $pTrace_level )
    return;

  $pLevelDir = $pOccurDir;

  if ( $pad > 1)
    $pLevelDir .= '/tag-' . $pLvl_cnt . '-' . $[$p] ['tag'] ;

  $pOccurDir = $pLevelDir;

  $pLevelDir [$p]= $pLevelDir;
  $pOccurDir [$p]= $pOccurDir;

  pFile_put_contents ( "$pLevelDir/level.json",     $ [$p] );  
  pFile_put_contents ( "$pLevelDir/pad-start.json", pTrace_get_pad_vars ()  );
  pFile_put_contents ( "$pLevelDir/app-start.json", pTrace_get_app_vars ()  );
  pFile_put_contents ( "$pLevelDir/html-base.html", $pBase[$p]        );
  pFile_put_contents ( "$pLevelDir/data.json",      $pData[$p]        );

?>
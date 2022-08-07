<?php

  if ( $pad > 1 ) {
    pFile_put_contents ( "$pLevelDir/html-result.html", $pResult[$p] );
    pFile_put_contents ( "$pLevelDir/pad-end.json",     pTrace_get_pad_vars ()  );
    pFile_put_contents ( "$pLevelDir/app-end.json",     pTrace_get_app_vars ()  );
  }

  if ($pad > 1) {
    $pLevelDir = $ [$pad-1] ['level_dir'];
    $pOccurDir = $ [$pad-1] ['occur_dir'];
  } 
  
?>
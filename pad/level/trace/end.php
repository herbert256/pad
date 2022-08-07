<?php

  if ( $p > 1 ) {
    pFile_put_contents ( $pLevelDir [$p] . "/html-result.html", $pResult [$p] );
    pFile_put_contents ( $pLevelDir [$p] . "/pad-end.json",     pTrace_get_pVars ()  );
    pFile_put_contents ( $pLevelDir [$p] . "/app-end.json",     pTrace_get_app_vars ()  );
  }

  if ($p > 1) {
    $pLevelDir [$p] = $ [$p-1] ['level_dir'];
    $pOccurDir [$p] = $ [$p-1] ['occur_dir'];
  } 
  
?>
<?php

  if ( $p > 1 ) {
    pFile_put_contents ( $pLevelDir [$p] . "/html-result.html", $pResult [$p] );
    pFile_put_contents ( $pLevelDir [$p] . "/pad-end.json",     pTraceGetVars ()  );
    pFile_put_contents ( $pLevelDir [$p] . "/app-end.json",     pTrace_get_app_vars ()  );
  }
  
?>
<?php

  if ( $pad > 1 ) {
    pFile_put_contents ( "$pLevel_dir/html-result.html", $pResult[$p] );
    pFile_put_contents ( "$pLevel_dir/pad-end.json",     pTrace_get_pad_vars ()  );
    pFile_put_contents ( "$pLevel_dir/app-end.json",     pTrace_get_app_vars ()  );
  }

  if ($pad > 1) {
    $pLevel_dir = $pParms [$pad-1] ['level_dir'];
    $pOccur_dir = $pParms [$pad-1] ['occur_dir'];
  } 
  
?>
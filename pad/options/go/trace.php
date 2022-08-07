<?php

  $pOpt_cnt++;

  $pOptions_dir = "$pLevel_dir/options/$pOption_name-$pOpt_cnt";

  pFile_put_contents ( "$pOptions_dir/data.html",    $pData[$p] );
  pFile_put_contents ( "$pOptions_dir/content.html", $pContent );
  pFile_put_contents ( "$pOptions_dir/base.html",    $pBase[$p] );

?>
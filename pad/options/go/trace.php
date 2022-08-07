<?php

  $pOpt_cnt++;

  $pOptions_dir = "$pLevel_dir/options/$pOption_name-$pOpt_cnt";

  pFile_put_contents ( "$pOptions_dir/data.html",    $pData[$pad] );
  pFile_put_contents ( "$pOptions_dir/content.html", $pContent );
  pFile_put_contents ( "$pOptions_dir/base.html",    $pBase[$pad] );

?>
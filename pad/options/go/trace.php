<?php

  $pOptCnt++;

  $pOptions_dir = $pLevelDir . "/options/$pOption_name-$pOptCnt";

  pFile_put_contents ( "$pOptions_dir/data.html",    $pData[$p] );
  pFile_put_contents ( "$pOptions_dir/content.html", $pContent );
  pFile_put_contents ( "$pOptions_dir/base.html",    $pBase[$p] );

?>
<?php

  $padOptCnt++;

  $padOptions_dir = $padLevelDir [$pad] . "/options/$padOption_name-$padOptCnt";

  pFile_put_contents ( "$padOptions_dir/data.html",    $padData [$pad] );
  pFile_put_contents ( "$padOptions_dir/content.html", $padContent );
  pFile_put_contents ( "$padOptions_dir/base.html",    $padBase [$pad] );

?>
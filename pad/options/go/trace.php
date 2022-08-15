<?php

  $padOptCnt++;

  $padOptions_dir = $padLevelDir [$pad] . "/options/$padOption_name-$padOptCnt";

  padFilePutContents ( "$padOptions_dir/data.html",    $padData [$pad] );
  padFilePutContents ( "$padOptions_dir/content.html", $padContent );
  padFilePutContents ( "$padOptions_dir/base.html",    $padBase [$pad] );

?>
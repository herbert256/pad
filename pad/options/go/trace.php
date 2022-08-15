<?php

  $padOptCnt++;

  $padOptionsDir = $padLevelDir [$pad] . "/options/$padOptionName-$padOptCnt";

  padFilePutContents ( "$padOptionsDir/data.html",    $padData [$pad] );
  padFilePutContents ( "$padOptionsDir/content.html", $padContent );
  padFilePutContents ( "$padOptionsDir/base.html",    $padBase [$pad] );

?>
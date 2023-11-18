<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];
  
  padFilePutContents ( "$padXmlDir/levels/$padXmlLvl/pad-base.pad", $padBase [$pad] );

  if ( ! padIsDefaultData ( $padData [$pad] ) )
    padFilePutContents ( "$padXmlDir/levels/$padXmlLvl/data.json", $padData [$pad] );

?>
<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];
  
  padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/pad-base.pad", $padBase [$pad] );

  if ( ! padIsDefaultData ( $padData [$pad] ) )
    padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/data.json", $padData [$pad] );

?>
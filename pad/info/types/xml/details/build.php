<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];
  
  padInfoPut ( "$padInfoDir/xml/details/$padXmlLvl/pad-base.pad", $padBase [$pad] );

  if ( ! padIsDefaultData ( $padData [$pad] ) )
    padInfoPut ( "$padInfoDir/xml/details/$padXmlLvl/data.json", $padData [$pad] );

?>
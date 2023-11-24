<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];
  
  padTailPut ( "$padTailDir/xml/details/$padXmlLvl/pad-base.pad", $padBase [$pad] );

  if ( ! padIsDefaultData ( $padData [$pad] ) )
    padTailPut ( "$padTailDir/xml/details/$padXmlLvl/data.json", $padData [$pad] );

?>
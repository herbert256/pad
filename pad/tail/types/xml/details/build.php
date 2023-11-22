<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];
  
  padTailPut ( "$padXmlDir/$padTailId/details/$padXmlLvl/pad-base.pad", $padBase [$pad] );

  if ( ! padIsDefaultData ( $padData [$pad] ) )
    padTailPut ( "$padXmlDir/$padTailId/details/$padXmlLvl/data.json", $padData [$pad] );

?>
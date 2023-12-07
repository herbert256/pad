<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];
 
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  if ( ! $padDefault [$pad] )
    padInfoPut ( "$padInfoDir/xml/details/$padXmlLvl/$padXmlOcc/result.pad", $padPad [$pad] );

?>
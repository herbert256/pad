<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];
 
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  if ( ! $padDefault [$pad] )
    padTailPut ( "$padTailDir/xml/details/$padXmlLvl/$padXmlOcc/result.pad", $padPad [$pad] );

?>
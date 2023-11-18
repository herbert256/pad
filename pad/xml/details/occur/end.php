<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];
 
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  if ( ! $padDefault [$pad] )
    padFilePutContents ( "$padXmlDir/levels/$padXmlLvl/$padXmlOcc/pad-end.pad", $padPad [$pad] );

?>
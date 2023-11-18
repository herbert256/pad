<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];
 
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  if ( ! $padDefault [$pad] )
    padFilePutContents ( "$padXmlDir/details/$padXmlLvl/$padXmlOcc/pad-end.pad", $padPad [$pad] );

?>
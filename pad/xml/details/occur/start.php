<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['id']     = $padXmlOcc;
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['type']   = $padOccurType [$pad];
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['childs'] = FALSE;
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size']   = 0;

  if ( ! $padDefault [$pad] ) {
    padFilePutContents ( "$padXmlDir/details/$padXmlLvl/$padXmlOcc/data.json",    $padCurrent [$pad] );
    padFilePutContents ( "$padXmlDir/details/$padXmlLvl/$padXmlOcc/pad-base.pad", $padPad     [$pad] );
  }

?>
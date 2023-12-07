<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['id']     = $padXmlOcc;
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['type']   = $padOccurType [$pad];
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['childs'] = FALSE;
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size']   = 0;

  if ( ! $padDefault [$pad] ) {
    padInfoPut ( "$padInfoDir/xml/details/$padXmlLvl/$padXmlOcc/data.json", $padCurrent [$pad] );
    padInfoPut ( "$padInfoDir/xml/details/$padXmlLvl/$padXmlOcc/base.pad",  $padPad     [$pad] );
  }

?>
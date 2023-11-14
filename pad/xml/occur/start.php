<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['id']     = $padXmlOcc;
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['type']   = $padOccurType [$pad];
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['childs'] = FALSE;
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size']   = 0;

  if ( ! $padDefault [$pad] ) {
    padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/$padXmlOcc/data.json",    $padCurrent [$pad] );
    padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/$padXmlOcc/pad-base.pad", $padPad     [$pad] );
  }

  $padXmlEventType = 'occur-start';
  include pad . 'xml/event.php';

?>
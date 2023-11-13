<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['id']     = $padXmlOcc;
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['childs'] = 0;
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['type']   = $padOccurType [$pad];
  $padXml [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size']   = 0;

  $padXmlEventType = 'occur-start';
  include pad . 'xml/event.php';

?>
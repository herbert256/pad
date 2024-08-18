<?php

  $padInfoXmlLvl = $padInfoXmlLevel [$pad];
  $padInfoXmlOcc = $padOccur    [$pad];
 
  $padInfoXmlTree [$padInfoXmlLvl] ['occurs'] [$padInfoXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  $padInfoXmlEventType = 'occur-end';
  include '/pad/info/types/xml/event.php';

?>
<?php

  $padInfXmlLvl = $padInfXmlLevel [$pad];
  $padInfXmlOcc = $padOccur    [$pad];
 
  $padInfXmlTree [$padInfXmlLvl] ['occurs'] [$padInfXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  $padInfXmlEventType = 'occur-end';
  include '/pad/info/types/xml/event.php';

?>
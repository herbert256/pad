<?php

  $padInfoXmlLvl = $padInfoXmlLevel [$pad];
  $padInfoXmlOcc = $padOccur    [$pad];

  $padInfoXmlTree [$padInfoXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  $padInfoXmlEventType = 'level-end';
  include '/pad/info/xml/event.php';

?>
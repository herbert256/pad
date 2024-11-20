<?php

  $padInfoXmlLvl = $padInfoXmlLevel [$pad];
  $padInfoXmlOcc = $padOccur    [$pad];

  $padInfoXmlTree [$padInfoXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  $padInfoXmlEventType = 'level-end';
  include 'info/types/xml/event.php';

?>
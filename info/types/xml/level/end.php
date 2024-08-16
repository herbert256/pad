<?php

  $padInfXmlLvl = $padInfXmlLevel [$pad];
  $padInfXmlOcc = $padOccur    [$pad];

  $padInfXmlTree [$padInfXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  $padInfXmlEventType = 'level-end';
  include '/pad/info/types/xml/event.php';

?>
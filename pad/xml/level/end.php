<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXml [$padXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  $padXmlEventType = 'level-end';
  include pad . 'xml/event.php';

?>
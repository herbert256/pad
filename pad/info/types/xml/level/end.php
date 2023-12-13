<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXmlTree [$padXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  $padXmlEventType = 'level-end';
  include pad . 'info/types/xml/event.php';

?>
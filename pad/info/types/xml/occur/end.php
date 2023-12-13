<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];
 
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  $padXmlEventType = 'occur-end';
  include pad . 'info/types/xml/event.php';

?>
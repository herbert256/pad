<?php

  global $padInfoXmlCompact;

  if ( $padInfoXmlCompact )
    return;

  $padInfoXmlLvl = $padInfoXmlLevel [$pad];
  $padInfoXmlOcc = $padOccur    [$pad];

  $padInfoXmlTree [$padInfoXmlLvl] ['occurs'] [$padInfoXmlOcc] ['size'] = strlen ( $padOut [$pad] );

  $padInfoXmlEventType = 'occur-end';
  include PAD . 'info/types/xml/event.php';

?>
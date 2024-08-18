<?php

  if ( ! $padInfoXrefXml )
    return;
  
  $padInfoXrefLvl = $padInfoXrefLevel [$pad];
  $padInfoXrefOcc = $padOccur     [$pad];

  $padInfoXrefStore [$padInfoXrefLvl] ['occurs'] [$padInfoXrefOcc] ['xref'] = [];

  $padInfoXrefEventType = 'occur-start';
  include '/pad/info/types/xref/event.php';

?>
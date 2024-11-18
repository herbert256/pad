<?php

  if ( ! $padInfoXrefXml )
    return;
  
  $padInfoXrefLvl = $padInfoXrefLevel [$pad];
  $padInfoXrefOcc = $padOccur     [$pad];

  $padInfoXrefStore [$padInfoXrefLvl] ['occurs'] [$padInfoXrefOcc] ['xref'] = [];

  $padInfoXrefEventType = 'occur-start';
  include PAD . 'info/types/xref/event.php';

?>
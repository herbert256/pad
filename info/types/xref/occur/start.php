<?php

  if ( ! $padInfXrefXml )
    return;
  
  $padInfXrefLvl = $padInfXrefLevel [$pad];
  $padInfXrefOcc = $padOccur     [$pad];

  $padInfXrefStore [$padInfXrefLvl] ['occurs'] [$padInfXrefOcc] ['xref'] = [];

  $padInfXrefEventType = 'occur-start';
  include '/pad/info/types/xref/event.php';

?>
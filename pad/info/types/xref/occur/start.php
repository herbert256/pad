<?php

  if ( ! $padXrefXml )
    return;
  
  $padXrefLvl = $padXrefLevel [$pad];
  $padXrefOcc = $padOccur     [$pad];

  $padXrefStore [$padXrefLvl] ['occurs'] [$padXrefOcc] ['xref'] = [];

  $padXrefEventType = 'occur-start';
  include pad . 'info/types/xref/event.php';

?>
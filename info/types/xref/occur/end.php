<?php

  if ( ! $padInfXrefXml )
    return;
  
  $padInfXrefLvl = $padInfXrefLevel [$pad];
  $padInfXrefOcc = $padOccur    [$pad];
 
  if ( strlen ( $padPad [$pad] ) )
    $padInfXrefStore [$padInfXrefLvl] ['occurs'] [$padInfXrefOcc] ['xref'] [] = 'size ' . strlen ( $padPad [$pad] );

  $padInfXrefEventType = 'occur-end';
  include '/pad/info/types/xref/event.php';

?>
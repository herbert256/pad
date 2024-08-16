<?php

  if ( ! $padInfoXrefXml )
    return;
  
  $padInfoXrefLvl = $padInfoXrefLevel [$pad];
  $padInfoXrefOcc = $padOccur    [$pad];
 
  if ( strlen ( $padPad [$pad] ) )
    $padInfoXrefStore [$padInfoXrefLvl] ['occurs'] [$padInfoXrefOcc] ['xref'] [] = 'size ' . strlen ( $padPad [$pad] );

  $padInfoXrefEventType = 'occur-end';
  include '/pad/info/xref/event.php';

?>
<?php

  if ( ! $padInfoXrefXml )
    return;
  
  $padInfoXrefLvl = $padInfoXrefLevel [$pad];
  $padInfoXrefOcc = $padOccur    [$pad];
 
  if ( strlen ( $padOut [$pad] ) )
    $padInfoXrefStore [$padInfoXrefLvl] ['occurs'] [$padInfoXrefOcc] ['xref'] [] = 'size ' . strlen ( $padOut [$pad] );

  $padInfoXrefEventType = 'occur-end';
  include 'info/types/xref/event.php';

?>
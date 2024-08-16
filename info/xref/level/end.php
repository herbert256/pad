<?php
  
  if ( ! $padInfoXrefXml )
    return;
  
  $padInfoXrefLvl = $padInfoXrefLevel [$pad];
  $padInfoXrefOcc = $padOccur     [$pad];
 
  if ( strlen ( $padResult [$pad] ) )
    $padInfoXrefStore [$padInfoXrefLvl] ['end'] [] = 'size ' . strlen ( $padResult [$pad] );

  $padInfoXrefEventType = 'level-end';
  include '/pad/info/xref/event.php';

?>
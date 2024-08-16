<?php
  
  if ( ! $padInfXrefXml )
    return;
  
  $padInfXrefLvl = $padInfXrefLevel [$pad];
  $padInfXrefOcc = $padOccur     [$pad];
 
  if ( strlen ( $padResult [$pad] ) )
    $padInfXrefStore [$padInfXrefLvl] ['end'] [] = 'size ' . strlen ( $padResult [$pad] );

  $padInfXrefEventType = 'level-end';
  include '/pad/info/types/xref/event.php';

?>
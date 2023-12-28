<?php
  
  if ( ! $padXrefXml )
    return;
  
  $padXrefLvl = $padXrefLevel [$pad];
  $padXrefOcc = $padOccur     [$pad];
 
  if ( strlen ( $padResult [$pad] ) )
    $padXrefStore [$padXrefLvl] ['end'] [] = 'size ' . strlen ( $padResult [$pad] );

  $padXrefEventType = 'level-end';
  include pad . 'info/types/xref/event.php';

?>
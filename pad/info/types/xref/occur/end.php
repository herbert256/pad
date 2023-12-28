<?php

  if ( ! $padXrefXml )
    return;
  
  $padXrefLvl = $padXrefLevel [$pad];
  $padXrefOcc = $padOccur    [$pad];
 
  if ( strlen ( $padPad [$pad] ) )
    $padXrefStore [$padXrefLvl] ['occurs'] [$padXrefOcc] ['xref'] [] = 'size ' . strlen ( $padPad [$pad] );

  $padXrefEventType = 'occur-end';
  include pad . 'info/types/xref/event.php';

?>
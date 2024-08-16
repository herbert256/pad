<?php

  if ( ! $padInfXrefXml )
    return;
  
  $padInfXrefNew            = [];
  $padInfXrefNew ['start']  = [];
  $padInfXrefNew ['end']    = [];
  $padInfXrefNew ['occurs'] = [];

  $padInfXrefStore [] = $padInfXrefNew;

  $padInfXrefLevel [$pad] = array_key_last ( $padInfXrefStore );

  $padInfXrefLvl = $padInfXrefLevel [$pad];
  $padInfXrefOcc = $padOccur     [$pad]; 
  
  $padInfXrefEventType = 'level-start';
  include '/pad/info/types/xref/event.php';

?>
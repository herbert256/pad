<?php

  if ( ! $padInfoXrefXml )
    return;
  
  $padInfoXrefNew            = [];
  $padInfoXrefNew ['start']  = [];
  $padInfoXrefNew ['end']    = [];
  $padInfoXrefNew ['occurs'] = [];

  $padInfoXrefStore [] = $padInfoXrefNew;

  $padInfoXrefLevel [$pad] = array_key_last ( $padInfoXrefStore );

  $padInfoXrefLvl = $padInfoXrefLevel [$pad];
  $padInfoXrefOcc = $padOccur     [$pad]; 
  
  $padInfoXrefEventType = 'level-start';
  include '/pad/info/types/xref/event.php';

?>
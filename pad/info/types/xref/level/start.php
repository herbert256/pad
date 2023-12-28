<?php

  if ( ! $padXrefXml )
    return;
  
  $padXrefNew            = [];
  $padXrefNew ['start']  = [];
  $padXrefNew ['end']    = [];
  $padXrefNew ['occurs'] = [];

  $padXrefStore [] = $padXrefNew;

  $padXrefLevel [$pad] = array_key_last ( $padXrefStore );

  $padXrefLvl = $padXrefLevel [$pad];
  $padXrefOcc = $padOccur     [$pad]; 
  
  $padXrefEventType = 'level-start';
  include pad . 'info/types/xref/event.php';

?>
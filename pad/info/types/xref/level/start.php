<?php

  if ( ! $padXrefXml )
    return;
  
  $padXrefNew            = [];
  $padXrefNew ['start']  = [];
  $padXrefNew ['end']    = [];
  $padXrefNew ['occurs'] = [];

  $padXrefTree [] = $padXrefNew;

  $padXrefLevel [$pad] = array_key_last ( $padXrefTree );

  $padXrefLvl = $padXrefLevel [$pad];
  $padXrefOcc = $padOccur     [$pad]; 
  
  $padXrefEventType = 'level-start';
  include pad . 'info/types/xref/event.php';

?>
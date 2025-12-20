<?php

  include PAD . 'info/types/xml/level/parent.php';

  $padInfoXmlNew             = [];
  $padInfoXmlNew ['tag']     = str_replace ( '/', '-', $padTag [$pad] );
  $padInfoXmlNew ['level']   = $pad;
  $padInfoXmlNew ['type']    = $padType [$pad];
  $padInfoXmlNew ['parm']    = $padOpt [$pad] [0];
  $padInfoXmlNew ['parms']   = [];
  $padInfoXmlNew ['occurs']  = [];
  $padInfoXmlNew ['childs']  = FALSE;
  $padInfoXmlNew ['written'] = FALSE;
  $padInfoXmlNew ['size']    = 0;
  $padInfoXmlNew ['result']  = '';
  $padInfoXmlNew ['source']  = '';
  $padInfoXmlNew ['start']   = [];
  $padInfoXmlNew ['end']     = [];

  $padInfoXmlTree [] = $padInfoXmlNew;

  $padInfoXmlLevel [$pad] = array_key_last ( $padInfoXmlTree );

  $padInfoXmlLvl = $padInfoXmlLevel [$pad];
  $padInfoXmlOcc = $padOccur    [$pad];

  $padInfoXmlEventType = 'level-start';
  include PAD . 'info/types/xml/event.php';

?>
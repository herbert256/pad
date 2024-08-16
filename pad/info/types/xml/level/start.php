<?php

  include '/pad/info/types/xml/level/parent.php';
    
  $padInfXmlNew             = [];
  $padInfXmlNew ['tag']     = str_replace ( '/', '-', $padTag [$pad] );
  $padInfXmlNew ['level']   = $pad;
  $padInfXmlNew ['type']    = $padType [$pad];
  $padInfXmlNew ['parm']    = $padOpt [$pad] [0];
  $padInfXmlNew ['parms']   = [];
  $padInfXmlNew ['occurs']  = [];
  $padInfXmlNew ['childs']  = FALSE;
  $padInfXmlNew ['written'] = FALSE;
  $padInfXmlNew ['size']    = 0;
  $padInfXmlNew ['result']  = '';
  $padInfXmlNew ['source']  = '';
  $padInfXmlNew ['start']   = [];
  $padInfXmlNew ['end']     = [];

  $padInfXmlTree [] = $padInfXmlNew;

  $padInfXmlLevel [$pad] = array_key_last ( $padInfXmlTree );

  $padInfXmlLvl = $padInfXmlLevel [$pad];
  $padInfXmlOcc = $padOccur    [$pad]; 
  
  $padInfXmlEventType = 'level-start';
  include '/pad/info/types/xml/event.php';

?>
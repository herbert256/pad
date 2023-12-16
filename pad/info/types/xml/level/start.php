<?php

  include pad . 'info/types/xml/level/parent.php';
    
  $padXmlTag = ( $padTag [$pad] == 'padBuildData' ) ? "page-$padStartPage" : $padTag [$pad];

  $padXmlNew             = [];
  $padXmlNew ['tag']     = str_replace ( '/', '-', $padXmlTag );
  $padXmlNew ['level']   = $pad;
  $padXmlNew ['type']    = $padType [$pad];
  $padXmlNew ['parm']    = $padOpt [$pad] [0];
  $padXmlNew ['parms']   = [];
  $padXmlNew ['occurs']  = [];
  $padXmlNew ['childs']  = FALSE;
  $padXmlNew ['written'] = FALSE;
  $padXmlNew ['size']    = 0;
  $padXmlNew ['result']  = '';
  $padXmlNew ['source']  = '';
  $padXmlNew ['start']   = [];
  $padXmlNew ['end']     = [];

  $padXmlTree [] = $padXmlNew;

  $padXmlLevel [$pad] = array_key_last ( $padXmlTree );

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad]; 
  
  $padXmlEventType = 'level-start';
  include pad . 'info/types/xml/event.php';

?>
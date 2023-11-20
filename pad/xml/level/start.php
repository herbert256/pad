<?php

  include pad . 'xml/level/parent.php';
    
  $padXmlTag = ( $padTag [$pad] == 'padBuildData' ) ? "page-$padPage" : $padTag [$pad];

  $padXmlNew            = [];
  $padXmlNew ['tag']    = str_replace ( '/', '-', $padXmlTag );
  $padXmlNew ['level']  = $pad;
  $padXmlNew ['type']   = $padType [$pad];
  $padXmlNew ['parm']   = $padOpt [$pad] [0];
  $padXmlNew ['parms']  = [];
  $padXmlNew ['occurs'] = [];
  $padXmlNew ['childs'] = FALSE;
  $padXmlNew ['size']   = 0;
  $padXmlNew ['result'] = '';
  $padXmlNew ['source'] = '';

  $padXmlTree [] = $padXmlNew;

  $padXmlLevel [$pad] = array_key_last ( $padXmlTree );

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  if ( $padXmlDetails) {
    $padXmlTree [$padXmlLvl] ['id']        = $padXmlLvl;
    $padXmlTree [$padXmlLvl] ['parent']    = $padXmlParent;
    $padXmlTree [$padXmlLvl] ['parentOcc'] = $padXmlParentOcc;
  }
  
  $padXmlEventType = 'level-start';
  include pad . 'xml/event.php';

  if ( $padXmlDetails )
    include pad . 'xml/details/level/start.php';

?>
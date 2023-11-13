<?php
  
  $padXmlTag = ( $padTag [$pad] == 'padBuildData' ) ? "page-$padPage" : $padTag [$pad];

  $padXmlNew = [];
  $padXmlNew ['tag']    = str_replace ( '/', '-', $padXmlTag );
  $padXmlNew ['level']  = $pad;
  $padXmlNew ['occurs'] = [];
  $padXmlNew ['parms']  = [];
  $padXmlNew ['type']   = $padType [$pad];
  $padXmlNew ['childs'] = FALSE;
  $padXmlNew ['size']   = 0;
  $padXmlNew ['raw']    = $padOpt [$pad] [0];

  $padXml [] = $padXmlNew;

  $padXmlLevel [$pad] = array_key_last ( $padXml );

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  if ( $pad > 0 ) {

    $padXmlParent    = $padXmlLevel [$pad-1];
    $padXmlParentOcc = $padOccur [$pad-1];
    
    $padXml [$padXmlParent] ['childs'] = TRUE;

    if ( $padXmlParentOcc > 0 and $padXmlParentOcc < 99999 )
      $padXml [$padXmlParent] ['occurs'] [$padXmlParentOcc] ['childs'] = TRUE;

  }

  $padXml [$padXmlLvl] ['result'] = '';
  $padXml [$padXmlLvl] ['source'] = '';
  
  $padXmlEventType = 'level-start';
  include pad . 'xml/event.php';

?>
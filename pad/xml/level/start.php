<?php
  
  $padXmlTag = ( $padTag [$pad] == 'padBuildData' ) ? "page-$padPage" : $padTag [$pad];

  $padXmlParent = $padXmlParentOcc = 0;
  if ( $pad > 0 ) {
    $padXmlParent    = $padXmlLevel [$pad-1];
    $padXmlParentOcc = $padOccur [$pad-1];
  }

  $padXmlNew               = [];
  $padXmlNew ['tag']       = str_replace ( '/', '-', $padXmlTag );
  $padXmlNew ['level']     = $pad;
  $padXmlNew ['type']      = $padType [$pad];
  $padXmlNew ['parm']      = $padOpt [$pad] [0];
  $padXmlNew ['parms']     = [];
  $padXmlNew ['parentID']  = $padXmlParent;
  $padXmlNew ['parentOCC'] = $padXmlParentOcc;

  $padXml [] = $padXmlNew;

  $padXmlLevel [$pad] = array_key_last ( $padXml );

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  padFilePutContents ( "$padXmlFile/levels/$padXmlLvl/level-entry.json", $padXmlNew );

  if ( $pad > 0 ) {

    $padXml [$padXmlParent] ['childs'] = TRUE;

    if ( $padXmlParentOcc > 0 and $padXmlParentOcc < 99999 )
      $padXml [$padXmlParent] ['occurs'] [$padXmlParentOcc] ['childs'] = TRUE;

  }

  $padXml [$padXmlLvl] ['occurs'] = [];
  $padXml [$padXmlLvl] ['childs'] = FALSE;
  $padXml [$padXmlLvl] ['size']   = 0;
  $padXml [$padXmlLvl] ['result'] = '';
  $padXml [$padXmlLvl] ['source'] = '';
  
  $padXmlEventType = 'level-start';
  include pad . 'xml/event.php';

?>
<?php
  
  $padXmlId++;
  $padXmlLevel [$pad] = $padXmlId;

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXmlTag = ( $padTag [$pad] == 'padBuildData' ) ? "page-$padPage" : $padTag [$pad];

  $padXml [$padXmlLvl] ['tag']     = str_replace ( '/', '-', $padXmlTag );
  $padXml [$padXmlLvl] ['level']   = $pad;
  $padXml [$padXmlLvl] ['occurs']  = [];
  $padXml [$padXmlLvl] ['parms']   = [];
  $padXml [$padXmlLvl] ['type']    = $padType [$pad];
  $padXml [$padXmlLvl] ['childs']  = 0;
  $padXml [$padXmlLvl] ['size']    = 0;
  $padXml [$padXmlLvl] ['raw']     = $padOpt [$pad] [0];

  if ( $pad > 0 ) {

    $padXmlParent    = $padXmlLevel [$pad-1];
    $padXmlParentOcc = $padOccur [$pad-1];
    
    $padXml [$padXmlParent] ['childs'] ++;

    if ( $padXmlParentOcc > 0 and $padXmlParentOcc < 99999 )
      $padXml [$padXmlParent] ['occurs'] [$padXmlParentOcc] ['childs'] ++;

  }

  $padXml [$padXmlLvl] ['result'] = '';
  $padXml [$padXmlLvl] ['source'] = '';
  
  $padXmlEventType = 'level-start';
  include pad . 'xml/event.php';

?>
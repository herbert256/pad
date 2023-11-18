<?php

  $padXmlParent = $padXmlParentOcc = 0;

  if ( $pad <= 0 ) 
    return;

  $padXmlParent    = $padXmlLevel [$pad-1];
  $padXmlParentOcc = $padOccur [$pad-1];

  $padXml [$padXmlParent] ['childs'] = TRUE;

  if ( $padXmlParentOcc > 0 and $padXmlParentOcc < 99999 )
    $padXml [$padXmlParent] ['occurs'] [$padXmlParentOcc] ['childs'] = TRUE;

?>
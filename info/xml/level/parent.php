<?php

  $padInfoXmlParent = $padInfoXmlParentOcc = 0;

  if ( $pad <= 0 ) 
    return;

  $padInfoXmlParent    = $padInfoXmlLevel [$pad-1];
  $padInfoXmlParentOcc = $padOccur [$pad-1];

  $padInfoXmlTree [$padInfoXmlParent] ['childs'] = TRUE;

  if ( $padInfoXmlParentOcc > 0 and $padInfoXmlParentOcc < 99999 )
    $padInfoXmlTree [$padInfoXmlParent] ['occurs'] [$padInfoXmlParentOcc] ['childs'] = TRUE;

?>
<?php

  $padInfXmlParent = $padInfXmlParentOcc = 0;

  if ( $pad <= 0 ) 
    return;

  $padInfXmlParent    = $padInfXmlLevel [$pad-1];
  $padInfXmlParentOcc = $padOccur [$pad-1];

  $padInfXmlTree [$padInfXmlParent] ['childs'] = TRUE;

  if ( $padInfXmlParentOcc > 0 and $padInfXmlParentOcc < 99999 )
    $padInfXmlTree [$padInfXmlParent] ['occurs'] [$padInfXmlParentOcc] ['childs'] = TRUE;

?>
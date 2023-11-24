<?php

  $padXmlParent = $padXmlParentOcc = 0;

  if ( $pad <= 0 ) 
    return;

  $padXmlParent    = $padXmlLevel [$pad-1];
  $padXmlParentOcc = $padOccur [$pad-1];

  $padXmlTree [$padXmlParent] ['childs'] = TRUE;

  if ( $padXmlParentOcc > 0 and $padXmlParentOcc < 99999 )
    $padXmlTree [$padXmlParent] ['occurs'] [$padXmlParentOcc] ['childs'] = TRUE;

?>
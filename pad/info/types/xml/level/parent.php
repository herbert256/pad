<?php

  // Tells the enclosing level of the 'xml' info mode that it has a child, so the renderer emits
  // it as an open/close element pair instead of a single self-closing line - and marks the
  // parent's current occurrence the same way.
  //
  // Included at the top of info/types/xml/level/start.php; at the root there is no parent, so it
  // just leaves $padInfoXmlParent and $padInfoXmlParentOcc at 0 and returns.

  $padInfoXmlParent = $padInfoXmlParentOcc = 0;

  if ( $pad <= 0 )
    return;

  $padInfoXmlParent    = $padInfoXmlLevel [$pad-1];
  $padInfoXmlParentOcc = $padOccur [$pad-1];

  $padInfoXmlTree [$padInfoXmlParent] ['childs'] = TRUE;

  if ( $padInfoXmlParentOcc > 0 and $padInfoXmlParentOcc < 99999 )
    $padInfoXmlTree [$padInfoXmlParent] ['occurs'] [$padInfoXmlParentOcc] ['childs'] = TRUE;

?>
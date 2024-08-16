<?php

  $padInfXmlLvl = $padInfXmlLevel [$pad];
  $padInfXmlOcc = $padOccur    [$pad];

  $padInfXmlTree [$padInfXmlLvl] ['occurs'] [$padInfXmlOcc] ['id']     = $padInfXmlOcc;
  $padInfXmlTree [$padInfXmlLvl] ['occurs'] [$padInfXmlOcc] ['childs'] = FALSE;
  $padInfXmlTree [$padInfXmlLvl] ['occurs'] [$padInfXmlOcc] ['size']   = 0;

  if ( ! isset ( $padInfXmlTree [$padInfXmlLvl] ['occurs'] [$padInfXmlOcc] ['xref'] ) )
    $padInfXmlTree [$padInfXmlLvl] ['occurs'] [$padInfXmlOcc] ['xref'] = [];

  $padInfXmlEventType = 'occur-start';
  include '/pad/info/types/xml/event.php';

?>
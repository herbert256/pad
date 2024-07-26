<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['id']     = $padXmlOcc;
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['childs'] = FALSE;
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size']   = 0;

  if ( ! isset ( $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['xref'] ) )
    $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['xref'] = [];

  $padXmlEventType = 'occur-start';
  include pad . 'info/types/xml/event.php';

?>
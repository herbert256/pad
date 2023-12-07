<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['id']     = $padXmlOcc;
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['type']   = $padOccurType [$pad];
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['childs'] = FALSE;
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size']   = 0;

  $padXmlEventType = 'occur-start';
  include pad . 'info/types/xml/event.php';

  if ( $padXmlDetails )
    include pad . 'info/types/xml/details/occur/start.php';

?>
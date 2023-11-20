<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];
 
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  $padXmlEventType = 'occur-end';
  include pad . 'xml/event.php';

  if ( $padXmlDetails )
    include pad . 'xml/details/occur/end.php';

?>
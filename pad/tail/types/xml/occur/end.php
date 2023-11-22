<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];
 
  $padXmlTree [$padXmlLvl] ['occurs'] [$padXmlOcc] ['size'] = strlen ( $padPad [$pad] );

  $padXmlEventType = 'occur-end';
  include pad . 'tail/types/xml/event.php';

  if ( $padXmlDetails )
    include pad . 'tail/types/xml/details/occur/end.php';

?>
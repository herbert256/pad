<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXmlTree [$padXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  $padXmlEventType = 'level-end';
  include pad . 'xml/event.php';

  if ( $padXmlDetails )
    include pad . 'xml/details/level/end.php';   

?>
<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXmlTree [$padXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  $padXmlEventType = 'level-end';
  include pad . 'tail/types/xml/event.php';

  if ( $padXmlDetails )
    include pad . 'tail/types/xml/details/level/end.php';   

?>
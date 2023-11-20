<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];

  $padXmlTree [$padXmlLvl] ['source'] = include pad . 'xml/level/status.php';

  if ( $padXmlDetails )
    include pad . 'xml/details/level/info.php';  

?>
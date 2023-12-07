<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];

  $padXmlTree [$padXmlLvl] ['source'] = include pad . 'info/types/xml/level/status.php';

  if ( $padXmlDetails )
    include pad . 'info/types/xml/details/level/info.php';  

?>
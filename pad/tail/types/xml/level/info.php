<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];

  $padXmlTree [$padXmlLvl] ['source'] = include pad . 'tail/types/xml/level/status.php';

  if ( $padXmlDetails )
    include pad . 'tail/types/xml/details/level/info.php';  

?>
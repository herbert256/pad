<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];

  $padXml [$padXmlLvl] ['result'] = $padXmlTagReturn;
  $padXml [$padXmlLvl] ['source'] = include pad . 'xml/level/status.php';

  if ( $padXref )
    include pad . 'xref/status.php';

  if ( $padXmlDetails )
    include pad . 'xml/details/level/info.php';  

?>
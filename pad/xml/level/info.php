<?php

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur     [$pad];

  $padXml [$padXmlLvl] ['result'] = $padXmlTagReturn;
  $padXml [$padXmlLvl] ['source'] = include pad . 'xml/level/status.php';

?>
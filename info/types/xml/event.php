<?php

  $padInfXmlId++;

  $padInfXmlEvent ['id']    = $padInfXmlId;
  $padInfXmlEvent ['event'] = $padInfXmlEventType;
  $padInfXmlEvent ['tree']  = $padInfXmlLevel [$pad];
  $padInfXmlEvent ['occur'] = $padOccur    [$pad];

  $padInfXmlEvents [] = $padInfXmlEvent;

?>
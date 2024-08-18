<?php

  $padInfoXmlId++;

  $padInfoXmlEvent ['id']    = $padInfoXmlId;
  $padInfoXmlEvent ['event'] = $padInfoXmlEventType;
  $padInfoXmlEvent ['tree']  = $padInfoXmlLevel [$pad];
  $padInfoXmlEvent ['occur'] = $padOccur [$pad];

  $padInfoXmlEvents [] = $padInfoXmlEvent;

?>
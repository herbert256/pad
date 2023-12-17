<?php

  $padXmlId++;

  $padXmlEvent ['id']    = $padXmlId;
  $padXmlEvent ['event'] = $padXmlEventType;
  $padXmlEvent ['tree']  = $padXmlLevel [$pad];
  $padXmlEvent ['occur'] = $padOccur    [$pad];

  $padXmlEvents [] = $padXmlEvent;

?>
<?php

  $padXmlId++;

  $padXmlEvent ['id']    = $padXmlId;
  $padXmlEvent ['event'] = $padXmlEventType;
  $padXmlEvent ['tree']  = $padXmlLevel [$pad];
  $padXmlEvent ['occur'] = $padOccur    [$pad];

  $padXmlEvents [] = $padXmlEvent;

  $padXmlLvl = $padXmlLevel [$pad];
  $padXmlOcc = $padOccur    [$pad];

  $padXmlP1 = str_replace ( 'level-', '', $padXmlEventType ); 
  $padXmlP1 = str_replace ( 'occur-', '', $padXmlP1 ); 

  padInfo ( 'xml', $padXmlP1, substr ( $padXmlEventType, 0, 5 ), $padTag [$pad] );

?>
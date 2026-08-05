<?php

  // Appends one entry to the event log of the 'xml' info mode.
  //
  // Included by the four level/ and occur/ start and end files, each of which has just set
  // $padInfoXmlEventType to level-start, level-end, occur-start or occur-end. The entry carries
  // a running id plus the node of the current level and its occurrence number, which is all
  // padInfoXml (info/types/xml/_lib.php) needs to replay the run in order afterwards.

  $padInfoXmlId++;

  $padInfoXmlEvent ['id']    = $padInfoXmlId;
  $padInfoXmlEvent ['event'] = $padInfoXmlEventType;
  $padInfoXmlEvent ['tree']  = $padInfoXmlLevel [$pad];
  $padInfoXmlEvent ['occur'] = $padOccur [$pad];

  $padInfoXmlEvents [] = $padInfoXmlEvent;

?>
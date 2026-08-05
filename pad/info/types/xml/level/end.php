<?php

  // Closes the tree node of a level in the 'xml' info mode, from events/levelEnd.php: records
  // how many characters the level produced - which is also what tells the renderer whether a
  // level may be dropped in compact mode - and logs a level-end event.

  $padInfoXmlLvl = $padInfoXmlLevel [$pad];
  $padInfoXmlOcc = $padOccur    [$pad];

  $padInfoXmlTree [$padInfoXmlLvl] ['size'] = strlen ( $padResult [$pad] );

  $padInfoXmlEventType = 'level-end';
  include PAD . 'info/types/xml/event.php';

?>
<?php

  // Closes the entry for one occurrence in the 'xml' info mode, from events/occurEnd.php:
  // records how many characters this pass produced and logs an occur-end event.
  //
  // Compact mode reports levels only, so it returns straight away.

  global $padInfoXmlCompact;

  if ( $padInfoXmlCompact )
    return;

  $padInfoXmlLvl = $padInfoXmlLevel [$pad];
  $padInfoXmlOcc = $padOccur    [$pad];

  $padInfoXmlTree [$padInfoXmlLvl] ['occurs'] [$padInfoXmlOcc] ['size'] = strlen ( $padOut [$pad] );

  $padInfoXmlEventType = 'occur-end';
  include PAD . 'info/types/xml/event.php';

?>
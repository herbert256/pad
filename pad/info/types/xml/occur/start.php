<?php

  // Opens the entry for one occurrence - one pass of a data iteration - inside the level's node
  // for the 'xml' info mode, from events/occurStart.php, and logs an occur-start event.
  //
  // Compact mode reports levels only, so it returns straight away and no occurrence is recorded.

  global $padInfoXmlCompact;

  if ( $padInfoXmlCompact )
    return;

  $padInfoXmlLvl = $padInfoXmlLevel [$pad];
  $padInfoXmlOcc = $padOccur    [$pad];

  $padInfoXmlTree [$padInfoXmlLvl] ['occurs'] [$padInfoXmlOcc] ['id']     = $padInfoXmlOcc;
  $padInfoXmlTree [$padInfoXmlLvl] ['occurs'] [$padInfoXmlOcc] ['childs'] = FALSE;
  $padInfoXmlTree [$padInfoXmlLvl] ['occurs'] [$padInfoXmlOcc] ['size']   = 0;

  if ( ! isset ( $padInfoXmlTree [$padInfoXmlLvl] ['occurs'] [$padInfoXmlOcc] ['xref'] ) )
    $padInfoXmlTree [$padInfoXmlLvl] ['occurs'] [$padInfoXmlOcc] ['xref'] = [];

  $padInfoXmlEventType = 'occur-start';
  include PAD . 'info/types/xml/event.php';

?>
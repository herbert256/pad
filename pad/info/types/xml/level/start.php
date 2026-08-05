<?php

  // Creates the tree node for a level in the 'xml' info mode, from events/setup.php.
  //
  // After telling the parent it has children, it appends a node holding the tag name, the level,
  // the type and the first option, with empty slots the later events fill in - parameters,
  // occurrences, size, result - and registers its index in $padInfoXmlLevel[$pad] so everything
  // else can find it back. Ends by logging a level-start event.

  include PAD . 'info/types/xml/level/parent.php';

  $padInfoXmlNew             = [];
  $padInfoXmlNew ['tag']     = str_replace ( '/', '-', $padTag [$pad] );
  $padInfoXmlNew ['level']   = $pad;
  $padInfoXmlNew ['type']    = $padType [$pad];
  $padInfoXmlNew ['parm']    = $padOpt [$pad] [0];
  $padInfoXmlNew ['parms']   = [];
  $padInfoXmlNew ['occurs']  = [];
  $padInfoXmlNew ['childs']  = FALSE;
  $padInfoXmlNew ['written'] = FALSE;
  $padInfoXmlNew ['size']    = 0;
  $padInfoXmlNew ['result']  = '';
  $padInfoXmlNew ['source']  = '';
  $padInfoXmlNew ['start']   = [];
  $padInfoXmlNew ['end']     = [];

  $padInfoXmlTree [] = $padInfoXmlNew;

  $padInfoXmlLevel [$pad] = array_key_last ( $padInfoXmlTree );

  $padInfoXmlLvl = $padInfoXmlLevel [$pad];
  $padInfoXmlOcc = $padOccur    [$pad];

  $padInfoXmlEventType = 'level-start';
  include PAD . 'info/types/xml/event.php';

?>
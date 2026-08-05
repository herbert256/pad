<?php

  // Fires from the tail of level/setup.php, the moment a new level's globals have been
  // initialised and before its parameters are parsed - the earliest point at which a level
  // exists. Opens the level's block in the trace and xml reports.

  global $padInfoTrace, $padInfoXml;

  if ( $padInfoTrace  ) include PAD . 'info/types/trace/level/start.php';
  if ( $padInfoXml    ) include PAD . 'info/types/xml/level/start.php';

?>
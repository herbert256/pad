<?php

  global $padInfoTrace, $padInfoXml;

  if ( $padInfoTrace ) include PAD . 'info/types/trace/level/end.php';
  if ( $padInfoXml   ) include PAD . 'info/types/xml/level/end.php';

  include PAD . 'events/options.php'

?>
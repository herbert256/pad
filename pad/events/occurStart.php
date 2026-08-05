<?php

  // Fires from occurrence/occurrence.php at the top of every iteration of a data tag, after
  // occurrence/init.php and before the row's values are bound, opening that occurrence in the
  // trace and xml reports.

  global $padInfoTrace, $padInfoXml;

  if ( $padInfoTrace ) include PAD . 'info/types/trace/occur/start.php';
  if ( $padInfoXml   ) include PAD . 'info/types/xml/occur/start.php';

?>
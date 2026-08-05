<?php

  // Fires from occurrence/end.php after one iteration's output has been appended to the level
  // result and before the occurrence state is reset, closing that occurrence in the trace and
  // xml reports.

  global $padInfoTrace, $padInfoXml;

  if ( $padInfoTrace ) include PAD . 'info/types/trace/occur/end.php';
  if ( $padInfoXml   ) include PAD . 'info/types/xml/occur/end.php';

?>
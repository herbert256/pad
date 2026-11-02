<?php

  // barcode:kind selects a registered kind from its subsystem. The type handler
  // runs the subsystem with the kind in the tag name and its own parameters unchanged.


  $padCodeKind = $padTag [$pad];

  $padTagGo = PAD . 'subs/codes/barcode';

  return include PAD . 'types/_go/tag.php';

?>

<?php

  // Type handler for a barcode type: {barcode:ean13 '400638133393'/}, or a bare tag naming a
  // file of subs/codes/types/ when nothing else has the name, {itf14 '1540014128876'/}. The
  // symbology is the tag's name, and the code is drawn as {barcode '...', type='ean13'/} draws
  // it, by subs/codes/barcode.php - run as a built-in tag's PHP half is run, types/_go/tag.php.

  $padCodeKind = $padTag [$pad];

  $padTagGo = PAD . 'subs/codes/barcode';

  return include PAD . 'types/_go/tag.php';

?>

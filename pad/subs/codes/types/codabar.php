<?php

  // codabar - Codabar, the barcode of libraries, blood banks and parcel services: digits and
  // - $ : / . + between a start and a stop letter, A, B, C or D - a text without them gets A
  // at both ends. Options: height, scale, title, plain, color, background.

  include_once PAD . 'subs/codes/_lib/codabar.php';

  return padCodeLinear ( padCodeCodabar ( padCodeText () ) );

?>

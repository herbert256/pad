<?php

  // databar - GS1 DataBar Omnidirectional (ISO/IEC 24724), the small barcode of fresh food,
  // loose produce and coupons: a GTIN of 8, 12, 13 or 14 digits with its check digit, (01) in
  // front or not, as the GTIN-14 in 96 modules between two finder patterns, (01) and the
  // fourteen digits under the bars (subs/codes/_lib/databar.php). Options: height, scale,
  // title, plain, color, background.

  include_once PAD . 'subs/codes/_lib/databar.php';

  return padCodeLinear ( padCodeDatabar ( padCodeText () ) );

?>

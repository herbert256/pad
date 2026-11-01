<?php

  // gs1128 - GS1-128, the barcode of the logistics label: GS1 element strings, each
  // application identifier in parentheses before its value - (00) an SSCC, (01) a GTIN, (10) a
  // batch, (17) an expiry date - in Code 128 that starts with FNC1, a FNC1 behind every value
  // of no predefined length that is not the last, the AIs in parentheses under the bars. The
  // values are checked as GS1 defines them: the digits, the length, the characters, the check
  // digit of a key and the dates (subs/codes/_lib/gs1.php). Options: height, scale, title,
  // plain, color, background.

  include_once PAD . 'subs/codes/_lib/gs1.php';

  return padCodeLinear ( padCodeGs1128 ( padCodeText () ) );

?>

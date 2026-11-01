<?php

  // code39 - Code 39, the barcode of industry and of warehouse labels: digits, capital letters,
  // space and - . $ / + %, between the start and stop symbol *. Options: check adds the check
  // symbol of modulo 43 behind the text; height, scale, title, plain, color, background.

  include_once PAD . 'subs/codes/_lib/code39.php';

  return padCodeLinear ( padCode39 ( padCodeText (), padCodeFlag ( 'check' ) ) );

?>

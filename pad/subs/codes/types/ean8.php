<?php

  // ean8 - EAN-8, the short EAN of a small package: eight digits, or seven and the check digit
  // is added, in two groups of four between the guard bars. Options: height, scale, title,
  // plain, color, background.

  return padCodeLinear ( padBarcodeModules ( padCodeText (), 'ean8' ) );

?>

<?php

  // ean13 - EAN-13, the number of a product in a shop: thirteen digits, or twelve and the check
  // digit is added. The first digit stands in front of the bars - it sets the parity of the
  // six after it - the other twelve under them in two groups, the guard bars drawn longer.
  // Options: height, scale, title, plain, color, background.

  return padCodeLinear ( padBarcodeModules ( padCodeText (), 'ean13' ) );

?>

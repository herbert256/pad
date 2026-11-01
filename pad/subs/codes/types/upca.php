<?php

  // upca - UPC-A, the product number of North America: twelve digits, or eleven and the check
  // digit is added - an EAN-13 that starts with 0, drawn with the same bars, the number system
  // digit in front and the check digit behind them. Options: height, scale, title, plain,
  // color, background.

  return padCodeLinear ( padBarcodeModules ( padCodeText (), 'upca' ) );

?>

<?php

  // upca - UPC-A, the product number of North America: twelve digits, or eleven and the check
  // digit is added - an EAN-13 that starts with 0, drawn with the same bars, the number system
  // digit in front and the check digit behind them. Options: addon= the two or five digits of
  // an add-on (subs/codes/_lib/addon.php); height, scale, title, plain,
  // color, background.

  include_once PAD . 'subs/codes/_lib/addon.php';

  return padCodeLinear ( padCodeAddon ( padBarcodeModules ( padCodeText (), 'upca' ), padTagParm ( 'addon' ) ) );

?>

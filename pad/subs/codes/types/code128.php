<?php

  // code128 - Code 128, the code of parcels and shipping labels: any printable ASCII, a run of
  // four digits or more packed two to a symbol in its set C, a check symbol of modulo 103.
  // What {barcode:auto} draws for a text that is no EAN. Options: height, scale, title, plain,
  // color, background.

  return padCodeLinear ( padBarcodeModules ( padCodeText (), 'code128' ) );

?>

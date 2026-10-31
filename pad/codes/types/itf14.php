<?php

  // itf14 - ITF-14, the GS1 number of a carton or a pallet of products: fourteen digits, or
  // thirteen and the check digit is added, in Interleaved 2 of 5 between the bearer bars above
  // and below that keep a scanner from reading half of it. Options: height, scale, title,
  // plain, color, background.

  include_once PAD . 'codes/_lib/itf.php';

  $padItfDigits = padBarcodeDigits ( padCodeText (), 'itf14', 14 );

  return padCodeLinear ( is_array ( $padItfDigits ) ? padCodeItf ( $padItfDigits [0], 4 ) : $padItfDigits );

?>

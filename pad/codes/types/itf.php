<?php

  // itf - Interleaved 2 of 5, the barcode of digits on cartons, tickets and film: any number of
  // digits, two to a symbol - an odd number gets a 0 in front. Options: check adds the check
  // digit of weights 3 and 1; height, scale, title, plain, color, background. An ITF-14 with
  // its bearer bars is itf14.

  include_once PAD . 'codes/_lib/itf.php';

  $padItfDigits = padCodeItfDigits ( padCodeText (), padCodeFlag ( 'check' ) );

  return padCodeLinear ( ctype_digit ( $padItfDigits ) ? padCodeItf ( $padItfDigits ) : $padItfDigits );

?>

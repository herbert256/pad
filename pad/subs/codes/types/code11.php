<?php
  // Code 11: digits/hyphens, checks=0/1/2; default both C and K check characters.
  include_once PAD . 'subs/codes/_lib/code11.php';
  $padCode11Checks = padTagParm ( 'checks', 2 );
  if ( is_numeric ( $padCode11Checks ) and is_finite ( (float) $padCode11Checks ) and floor ( (float) $padCode11Checks ) == $padCode11Checks )
    $padCode11Checks = (int) $padCode11Checks;
  return padCodeLinear ( padCode11 ( padCodeText ( FALSE ), $padCode11Checks ) );
?>

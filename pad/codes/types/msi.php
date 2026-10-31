<?php

  // msi - MSI, the Plessey code of shelf labels: digits only, with a check digit of modulo 10.
  // Options: check= mod10 (default), mod1010 for a second check digit, or none; height,
  // scale, title, plain, color, background.

  include_once PAD . 'codes/_lib/msi.php';

  $padMsiCheck = padTagParm ( 'check', 'mod10' );

  return padCodeLinear ( padCodeMsi ( padCodeText (), $padMsiCheck === TRUE ? 'mod10' : strtolower ( trim ( (string) $padMsiCheck ) ) ) );

?>

<?php

  // code93 - Code 93, the denser successor of Code 39: every printable ASCII character, the
  // ones Code 39 lacks as a pair of a shift symbol and a letter, with its two check symbols of
  // modulo 47 always. Options: height, scale, title, plain, color, background.

  include_once PAD . 'subs/codes/_lib/code93.php';

  return padCodeLinear ( padCode93 ( padCodeText () ) );

?>

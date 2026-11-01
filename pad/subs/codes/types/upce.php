<?php

  // upce - UPC-E, the UPC-A of a small package with its zeros left out: six digits, or seven
  // with the number system (0 or 1) in front, or eight with the check digit behind - which is
  // that of the UPC-A they stand for, added when left off. The 11 or 12 digits of a UPC-A are
  // written in their short form, when they have one. Options: addon= the two or five digits of
  // an add-on (subs/codes/_lib/addon.php); height, scale, title, plain,
  // color, background.

  include_once PAD . 'subs/codes/_lib/upce.php';
  include_once PAD . 'subs/codes/_lib/addon.php';

  return padCodeLinear ( padCodeAddon ( padCodeUpce ( padCodeText () ), padTagParm ( 'addon' ) ) );

?>

<?php

  // ColorBrewer's multi-hue scales YlGnBu and YlOrRd in nine steps are their nine classes as
  // ColorBrewer 2.0 publishes them - the answer written from colorbrewer_schemes.js - and
  // without steps they give the same nine.

  $ylgnbu = implode ( ' ', padPalette ( 'ylgnbu', 9 ) );
  $ylorrd = implode ( ' ', padPalette ( 'ylorrd', 9 ) );
  $same   = ( padPalette ( 'ylgnbu' ) === padPalette ( 'ylgnbu', 9 ) and padPalette ( 'ylorrd' ) === padPalette ( 'ylorrd', 9 ) ) ? 'same' : 'differ';

?>

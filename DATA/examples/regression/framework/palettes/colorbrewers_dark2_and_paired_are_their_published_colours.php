<?php

  // ColorBrewer's qualitative Dark2 and Paired without steps are their eight and twelve colours
  // as ColorBrewer 2.0 publishes them - the answer written from colorbrewer_schemes.js - and the
  // thirteenth of Paired is its first again.

  $dark2  = implode ( ' ', padPalette ( 'dark2' ) );
  $paired = implode ( ' ', padPalette ( 'paired' ) );
  $again  = padPalette ( 'paired', 13 ) [12];

?>

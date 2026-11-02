<?php

  // ColorBrewer's diverging scales RdYlBu and PuOr in eleven steps are their eleven classes as
  // ColorBrewer 2.0 publishes them, PuOr in ColorBrewer's order from its orange end - the
  // answer written from colorbrewer_schemes.js - and one step of each is its middle class.

  $rdylbu = implode ( ' ', padPalette ( 'rdylbu', 11 ) );
  $puor   = implode ( ' ', padPalette ( 'puor', 11 ) );
  $middle = padPalette ( 'rdylbu', 1 ) [0] . ' ' . padPalette ( 'puor', 1 ) [0];

?>

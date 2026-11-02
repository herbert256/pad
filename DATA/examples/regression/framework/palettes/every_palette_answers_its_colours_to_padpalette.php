<?php

  // Every palette of pad/subs/themes/palettes/types/, as padPalette answers it: a scale in three steps
  // - its first colour, its middle and its last - a categorical palette its first three, and
  // the number of colours each gives without steps.

  include_once PAD . 'subs/themes/palettes/_lib/palette.php';

  $palettes = [];

  foreach ( padPaletteTypes () as $name )
    $palettes [] = [ 'name'   => $name,
                     'type'   => padPaletteData ( $name ) ['type'],
                     'three'  => implode ( ' ', padPalette ( $name, 3 ) ),
                     'count'  => count ( padPalette ( $name ) ) ];

?>

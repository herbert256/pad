<?php

  // {pad:palette 'viridis', steps=7/} - the colours of a palette as swatches with their hex
  // values: a palette is a file of subs/palettes/types/ - viridis, okabe-ito, blues, rdbu ...
  // - drawn by the palettes subsystem, subs/palettes/palette.php. The same palettes colour a
  // chart, {chart:bar ..., palette='viridis'/}, and padPalette ( 'viridis', 7 ) gives a
  // page's PHP their colours.

  return include PAD . 'subs/palettes/palette.php';

?>

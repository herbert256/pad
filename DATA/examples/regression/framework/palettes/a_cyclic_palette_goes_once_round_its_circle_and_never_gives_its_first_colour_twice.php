<?php

  // romaO, a cyclic palette: four steps are its quarters - entries 0, 64, 128 and 192 of the
  // 256 of its table - the last short of the first again -
  // sampled to its end the fourth would be its first once more - one step is its first, and
  // without steps it gives twelve colours, none of them twice.

  $four     = implode ( ' ', padPalette ( 'romao', 4 ) );
  $one      = implode ( ' ', padPalette ( 'romao', 1 ) );
  $twelve   = padPalette ( 'romao' );
  $count    = count ( $twelve );
  $distinct = count ( array_unique ( $twelve ) );

?>

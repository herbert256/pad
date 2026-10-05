<?php

  // Handles the row option: keeps a single row of the tag's data set, counted from 1.
  //
  // padHandGo() is given the same value for start and end, so every other row is dropped.
  //
  // A bare row is row 1, as a bare first is the first row: the TRUE it is went to padHandGo
  // as both ends, and every row number compared with TRUE as a boolean - never below it,
  // never above it - so a bare row kept every row.

  $padHandStart = $padHandEnd = ( $padHandParm === TRUE ) ? 1 : $padPrm [$pad] ['row'];

  padHandGo ( $padData [$pad], $padHandStart, $padHandEnd );

?>

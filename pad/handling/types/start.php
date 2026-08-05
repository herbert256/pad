<?php

  // Handles the start option, and the end and rows options that delegate here: keeps the
  // rows between start and end of the tag's data set, counted from 1.
  //
  // start defaults to 1 and end to the last row; negative values count back from the end
  // (start="-3" is the third row from the back, end="-1" drops the last row). When only
  // end and rows are given, the start is worked back from the end. padHandGo() then drops
  // everything outside the window, with rows as a maximum.

  $padHandCount = count ( $padData [$pad] );
  $padHandStart = $padPrm [$pad] ['start'] ?? 1;
  $padHandEnd   = $padPrm [$pad] ['end']   ?? $padHandCount;
  $padHandRows  = $padPrm [$pad] ['rows']  ?? 0 ;

  if ( $padHandStart < 0 )
    $padHandStart = $padHandCount + $padHandStart + 1;

  if ( $padHandEnd < 0 )
    $padHandEnd = $padHandCount + $padHandEnd;

  if ( ! isset ( $padPrm [$pad] ['start'] ) )
    if ( isset ( $padPrm [$pad] ['rows'] ) )
      $padHandStart = $padHandEnd - $padHandRows + 1;

  padHandGo ( $padData [$pad], $padHandStart, $padHandEnd, $padHandRows );

?>
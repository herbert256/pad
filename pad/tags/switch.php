<?php

  // The {switch 'odd', 'even'} tag: yields the next value of a rotating list, one step
  // further on every call - the usual way to stripe alternate table rows.
  //
  // The position is kept in $padSwNow, keyed by $padOpt [$pad] [0], the raw text of the tag's
  // parameters. Two switch tags written identically therefore share one rotation and two
  // written differently never do, and the counter lives for the whole request.

  $padSw = $padOpt [$pad] [0];

  if ( isset ( $padSwNow [$padSw] ) )
    $padSwNow [$padSw]++;
  else
    $padSwNow [$padSw] = 0;

  $padSwCnt = count ( $padOpt [$pad] ) - 1;
  $padSwIdx = $padSwNow [$padSw] % $padSwCnt + 1 ;

  return $padOpt [$pad] [$padSwIdx];

?>
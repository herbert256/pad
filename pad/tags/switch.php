<?php

  // The {switch 'odd', 'even'} tag: yields the next value of a rotating list, one step
  // further on every call - the usual way to stripe alternate table rows.
  //
  // The position is kept in $padSwNow, keyed by $padOpt [$pad] [0], the raw text of the tag's
  // parameters. Two switch tags written identically therefore share one rotation and two
  // written differently never do, and the counter lives for the whole request - except
  // across a nested pass, which takes its counting with it: engine state is restored when
  // any pass returns, so a rotation counted inside one is rolled back with the rest.
  // Seeded in inits/vars.php so every pass scope shares the one counter.

  $padSw = $padOpt [$pad] [0];

  if ( isset ( $padSwNow [$padSw] ) )
    $padSwNow [$padSw]++;
  else
    $padSwNow [$padSw] = 0;

  $padSwCnt = count ( $padOpt [$pad] ) - 1;
  $padSwIdx = $padSwNow [$padSw] % $padSwCnt + 1 ;

  return $padOpt [$pad] [$padSwIdx];

?>
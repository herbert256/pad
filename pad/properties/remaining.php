<?php

  // The remaining@tag property: how many occurrences of level $padIdx are still to come,
  // clamped at 0 for levels that outlive their data set. Counterpart of done.

  // An @start@ prelude comes before every row of the level, an @end@ coda after the last:
  // there all of its rows remain, here none, where the stand-in row the section renders on
  // answered 0 in the prelude beside a count@ of 3. The prelude is the section whose body is
  // still put aside in $padStartBase (level/start_end/).

  global $padData, $padOccur, $padSectionRows, $padStartBase;

  if ( isset ( $padSectionRows [$padIdx] ) )
    return ( ( $padStartBase [$padIdx] ?? '' ) !== '' ) ? $padSectionRows [$padIdx] : 0;

  $padWrk = count ( $padData [$padIdx] ) - $padOccur [$padIdx];

  if ($padWrk < 0)
    return 0;
  else
    return $padWrk;

?>

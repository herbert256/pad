<?php

  // The data@tag property: the whole data set of level $padIdx, every row of it.
  //
  // Returns an array rather than a scalar, so it is meant to be used as a data source
  // ({data@users}) instead of printed.

  // An @start@ prelude or @end@ coda renders on one stand-in row, the level's own rows kept
  // aside in $padStartData (level/start_end/) - those are its data, as count.php counts
  // them: {data@xs}...{/data@xs} in a prelude iterated the one empty row.

  global $padData, $padSectionRows, $padStartData;

  if ( isset ( $padSectionRows [$padIdx] ) )
    return $padStartData [$padIdx] ?? [];

  return $padData [$padIdx];

?>

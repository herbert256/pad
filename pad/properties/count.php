<?php

  // The count@tag property: how many occurrences level $padIdx has.
  //
  // Normally the number of rows in its data set, but the occurrence counter wins when it
  // has run past that - a level that keeps producing occurrences as it iterates.
  //
  // An @start@ prelude or @end@ coda renders on a single default row; while it does, the
  // level's own rows are counted, which level/start_end/ keeps in $padSectionRows - counted
  // as they stood, the prelude said 1 and the coda one more than the rows it followed.

  global $padData, $padOccur, $padSectionRows;

  if ( isset ( $padSectionRows [$padIdx] ) )
    return $padSectionRows [$padIdx];

  return max(count($padData[$padIdx]), $padOccur [$padIdx]);

?>
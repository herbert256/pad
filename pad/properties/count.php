<?php

  // The count@tag property: how many occurrences level $padIdx has.
  //
  // Normally the number of rows in its data set, but the occurrence counter wins when it
  // has run past that - a level that keeps producing occurrences as it iterates.

  global $padData, $padOccur;

  return max(count($padData[$padIdx]), $padOccur [$padIdx]);

?>
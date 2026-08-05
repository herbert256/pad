<?php

  // The data@tag property: the whole data set of level $padIdx, every row of it.
  //
  // Returns an array rather than a scalar, so it is meant to be used as a data source
  // ({data@users}) instead of printed.

  global $padData;

  return $padData [$padIdx];

?>
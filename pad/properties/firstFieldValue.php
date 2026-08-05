<?php

  // The firstFieldValue@tag property: the value of the first field of the row being
  // rendered, or '' when that row is empty - the short way to print a one-column data set.

  global $padCurrent;

  foreach ( $padCurrent [$padIdx] as $padV )
    return $padV;

  return '';

?>
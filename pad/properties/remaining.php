<?php

  // The remaining@tag property: how many occurrences of level $padIdx are still to come,
  // clamped at 0 for levels that outlive their data set. Counterpart of done.

  global $padData, $padOccur;

  $padWrk = count ( $padData [$padIdx] ) - $padOccur [$padIdx];

  if ($padWrk < 0)
    return 0;
  else
    return $padWrk;

?>
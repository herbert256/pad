<?php

  // The current@tag property: the number of the occurrence being rendered, counted from 1.
  //
  // The raw occurrence counter of level $padIdx, on which first, even, odd, done and
  // remaining are all based.

  global $padOccur;

  return $padOccur [$padIdx];

?>
<?php

  // The done@tag property: how many occurrences of level $padIdx are finished, i.e. the
  // current occurrence number minus one. Counterpart of remaining.

  global $padOccur;

  return $padOccur [$padIdx] - 1;

?>
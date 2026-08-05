<?php

  // The level group: look the path $names up in the complete data set of level $padIdx,
  // all occurrences rather than only the one currently being iterated.

  global $padData;

  return padAtSearch ( $padData [$padIdx], $names );

?>
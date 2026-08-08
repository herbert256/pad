<?php

  // The level group: look the path $names up in the complete data set of level $padIdx,
  // all occurrences rather than only the one currently being iterated.

  // A level that carries no data of its own holds the default sentinel, and an ordinal
  // path resolves against that to an empty answer - a hit, which would stop the walk at the
  // innermost level before it ever reaches the loop that has the rows. Default data is a
  // miss here.

  global $padData;

  if ( padIsDefaultData ( $padData [$padIdx] ?? [] ) )
    return INF;

  return padAtSearch ( $padData [$padIdx], $names );

?>
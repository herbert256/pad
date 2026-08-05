<?php

  // The first@tag property: TRUE while level $padIdx renders its first occurrence.
  //
  // Asks properties/current.php for the occurrence number and compares it with 1, so it
  // does not need to know how long the data set is. notFirst.php is its negation.

  global $padData, $padKey, $padOccur;

  return ( (include PAD . "properties/current.php") == 1 );

?>
<?php

  // The last@tag property: TRUE while level $padIdx renders its final occurrence.
  //
  // Compares the current key with the last key of the data set, so unlike first it can
  // only be answered once the whole set is known. notLast.php is its negation.
  //
  // Strictly: both sides are the array's own keys, an int or a string, and compared loosely
  // a key '02' equals a last key 2 - a set keyed '01', '1', '02', '2' had two last rows.

  global $padData, $padKey;

  return ( $padKey [$padIdx] === array_key_last ( $padData [$padIdx] ) );

?>
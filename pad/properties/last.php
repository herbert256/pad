<?php

  // The last@tag property: TRUE while level $padIdx renders its final occurrence.
  //
  // Compares the current key with the last key of the data set, so unlike first it can
  // only be answered once the whole set is known. notLast.php is its negation.

  global $padData, $padKey;

  return ( $padKey [$padIdx] == array_key_last ( $padData [$padIdx] ) );

?>
<?php

  // Handles the random option: replaces the tag's data with a random pick of its rows.
  //
  // pqRandom() does the picking on the keys, honouring the orderly option (keep the rows
  // in their original order) and the duplicates option (allow the same row more than
  // once); a bare random takes every row, in random order. The result is rebuilt with
  // fresh numeric keys.

  $padHandRandKeys       = array_keys ( $padData [$pad] );
  $padHandRandCount      = $padPrm [$pad] ['random']      ?? 0;
  $padHandRandOrderly    = $padPrm [$pad] ['orderly']     ?? 0;
  $padHandRandDuplicates = $padPrm [$pad] ['duplicates']  ?? 0;

  $padHandRandKeys = pqRandom ( $padHandRandKeys, $padHandRandCount, $padHandRandOrderly, $padHandRandDuplicates );

  $padHandRand    = $padData [$pad];
  $padData [$pad] = [];

  foreach ( $padHandRandKeys as $padK )
    $padData [$pad] [] = $padHandRand [$padK];

?>
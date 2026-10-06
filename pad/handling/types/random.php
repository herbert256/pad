<?php

  // Handles the random option: replaces the tag's data with a random pick of its rows.
  //
  // pqRandom() does the picking on the keys, honouring the orderly option (keep the rows
  // in their original order) and the duplicates option (allow the same row more than
  // once); a bare random takes every row, in random order. The result is rebuilt with
  // fresh numeric keys for numbered rows; named keys are kept.
  //
  // The count is the handler's count - a plain number, or 1 when the value is none, as
  // first= and last= take it - and a bare random is every row, which it says to pqRandom
  // with TRUE: a count of 0 is a count there, and draws no row, as first=0 takes none. The raw value went to
  // pqRandom as it was written: random='abc' compared as text above any count of rows, and
  // the duplicates loop it went to picked until the request ran out of memory; random='-1'
  // and random='2.5' ended the request on array_rand's ValueError and a deprecation.

  $padHandRandKeys       = array_keys ( $padData [$pad] );
  $padHandRandCount      = ( $padHandParm === TRUE ) ? TRUE : (int) $padHandCnt;
  $padHandRandOrderly    = $padPrm [$pad] ['orderly']     ?? 0;
  $padHandRandDuplicates = $padPrm [$pad] ['duplicates']  ?? 0;

  $padHandRandKeys = pqRandom ( $padHandRandKeys, $padHandRandCount, $padHandRandOrderly, $padHandRandDuplicates );

  $padHandRand    = $padData [$pad];
  $padData [$pad] = [];

  // Numbered rows are renumbered, named keys stay - as array_slice treats them for first and
  // last. negative depends on it: it names the rows x0, x1, ... and keeps those whose key
  // the handler did not hand back, so fresh numbers made it keep every row.

  foreach ( $padHandRandKeys as $padK )
    if ( is_string ( $padK ) and ! $padHandRandDuplicates )
      $padData [$pad] [$padK] = $padHandRand [$padK];
    else
      $padData [$pad] [] = $padHandRand [$padK];

?>

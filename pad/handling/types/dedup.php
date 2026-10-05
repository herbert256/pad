<?php

  // Handles the dedup option: drops duplicate rows from a single-field data set.
  //
  // Only rows holding exactly one field, named after the level ($padName [$pad]), take
  // part; their value, as a string, is the key two rows are told equal by, and of equal
  // values only the first row stays. A data set with no such row is left untouched.
  //
  // The key is the string form, or the serialized form of a value that is not a scalar: the
  // value itself as a key cut a float down to an integer - 1.5 and 1.7 were one entry, and
  // PHP 8.5 refuses the conversion outright - and an array cannot be a key at all.
  //
  // The first row of each value is kept under its own key, as where and page keep theirs.
  // negative tells the rows a handler kept by their keys, and the rows came back keyed by
  // their values: none of them matched, so dedup, negative kept every row where it was to
  // keep the repeats.

  $padDedup     = [];
  $padDedupSeen = [];

  foreach ( $padData [$pad] as $padK => $padV)
    if ( is_array ($padV) and count($padV) == 1 and isset ( $padV [$padName [$pad]] ) ) {
      $padDedupVal = $padV [$padName [$pad]];
      $padDedupKey = is_scalar ( $padDedupVal ) ? (string) $padDedupVal : serialize ( $padDedupVal );
      if ( isset ( $padDedupSeen [$padDedupKey] ) )
        continue;
      $padDedupSeen [$padDedupKey] = TRUE;
      $padDedup [$padK] = [ $padName [$pad] => $padDedupVal ] ;
    }

  if ( count ($padDedup) )
    $padData [$pad] = $padDedup;

?>
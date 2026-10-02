<?php

  // Handles the dedup option: drops duplicate rows from a single-field data set.
  //
  // Only rows holding exactly one field, named after the level ($padName [$pad]), take
  // part; their value, as a string, doubles as the array key, so equal values collapse into
  // one entry. A data set with no such row is left untouched.
  //
  // The key is the string form, or the serialized form of a value that is not a scalar: the
  // value itself as a key cut a float down to an integer - 1.5 and 1.7 were one entry, and
  // PHP 8.5 refuses the conversion outright - and an array cannot be a key at all.

  $padDedup = [];

  foreach ( $padData [$pad] as $padK => $padV)
    if ( is_array ($padV) and count($padV) == 1 and isset ( $padV [$padName [$pad]] ) ) {
      $padDedupVal = $padV [$padName [$pad]];
      $padDedupKey = is_scalar ( $padDedupVal ) ? (string) $padDedupVal : serialize ( $padDedupVal );
      $padDedup [ $padDedupKey ] = [ $padName [$pad] => $padDedupVal ] ;
    }

  if ( count ($padDedup) )
    $padData [$pad] = $padDedup;

?>
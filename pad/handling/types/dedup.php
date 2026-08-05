<?php

  // Handles the dedup option: drops duplicate rows from a single-field data set.
  //
  // Only rows holding exactly one field, named after the level ($padName [$pad]), take
  // part; their value doubles as the array key, so equal values collapse into one entry.
  // A data set with no such row is left untouched.

  $padDedup = [];

  foreach ( $padData [$pad] as $padK => $padV)
    if ( is_array ($padV) and count($padV) == 1 and isset ( $padV [$padName [$pad]] ) )
      $padDedup [ $padV [$padName [$pad]] ] = [ $padName [$pad] => $padV [$padName [$pad]] ] ;

  if ( count ($padDedup) )
    $padData [$pad] = $padDedup;

?>
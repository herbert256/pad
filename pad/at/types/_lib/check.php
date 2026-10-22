<?php

  // Searches the data stores that are already defined. When the reference named one
  // ($type) that store is tried first; otherwise, and if it did not match, every store in
  // $padDataStore is tried in turn. padMiss when no store holds the path $names.

  global $padDataStore;

  if ( ! isset ( $padDataStore ) or ! is_array ( $padDataStore ) )
    return padMiss;

  if ( $type and isset ( $padDataStore [$type] ) ) {
    $check = padAtSearch ( $padDataStore [$type], $names );
    if ( $check !== padMiss )
      return $check;
  }

  foreach ( $padDataStore as $value) {
    $check = padAtSearch ( $value, $names, $padAtNoDeep ?? 0 );
    if ( $check !== padMiss )
      return $check;
  }

  return padMiss;

?>

<?php

  // Last resort for a data lookup: the reference names a store ($type) that has not been
  // defined yet, so padAtDataNew() builds it through padData() and searches it, keeping it
  // only if the path $names was found. INF otherwise.

  global $padDataStore;

  if ( $type and ! isset ( $padDataStore [$type] ) ) {

    $check = padAtDataNew ( $type, $names );

    if ( $check !== INF )
      return $check;

  }

  return INF;

?>
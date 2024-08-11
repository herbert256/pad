<?php

  global $padDataStore;

  if ( $type and ! isset ( $padDataStore [$type] ) ) {

    $check = padAtDataNew ( $type, $names );

    if ( $check !== INF ) 
      return $check;

  }

  return INF;

?>
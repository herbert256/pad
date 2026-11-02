<?php

  function padIntervals ( $kind, $left, $right = [], $options = [] ) {
    include_once PAD . 'subs/intervals/_lib/intervals.php';
    try {
      if ( ! is_string ( $kind ) || ! padKind ( 'intervals', $kind ) || ! is_array ( $options ) )
        throw new InvalidArgumentException ( 'expected a known interval kind and options array' );
      $type = include PAD . "subs/intervals/types/$kind.php";
      return $type ['call'] ( $left, $right, $options );
    } catch ( InvalidArgumentException $e ) {
      padError ( 'padIntervals: ' . $e->getMessage () ); return NULL;
    }
  }

?>

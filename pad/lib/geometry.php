<?php
  function padGeometry ( $kind, $points, $options = [] ) {
    include_once PAD . 'subs/geometry/_lib/geometry.php';
    try {
      if ( ! is_string ( $kind ) || ! padKind ( 'geometry', $kind ) || ! is_array ( $options ) ) throw new InvalidArgumentException ( 'unknown geometry kind or invalid options' );
      $type = include PAD . "subs/geometry/types/$kind.php";
      return $type ['call'] ( $points, $options );
    } catch ( InvalidArgumentException $e ) { padError ( 'padGeometry: ' . $e->getMessage () ); return NULL; }
  }
?>

<?php
  function padColor ( $kind, $one, $two = NULL, $options = [] ) {
    include_once PAD . 'subs/colors/_lib/colors.php';
    try {
      if ( ! is_string ( $kind ) || ! padKind ( 'colors', $kind ) || ! is_array ( $options ) ) throw new InvalidArgumentException ( 'unknown color kind or invalid options' );
      $type = include PAD . "subs/colors/types/$kind.php";
      return $type ['call'] ( $one, $two, $options );
    } catch ( InvalidArgumentException $e ) { padError ( 'padColor: ' . $e->getMessage () ); return NULL; }
  }
?>

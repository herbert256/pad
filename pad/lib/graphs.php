<?php
  function padGraph ( $kind, $nodes, $edges, $options = [] ) {
    include_once PAD . 'subs/graphs/_lib/graphs.php';
    try {
      if ( ! is_string ( $kind ) || ! padKind ( 'graphs', $kind ) || ! is_array ( $options ) )
        throw new InvalidArgumentException ( 'unknown kind or invalid options' );
      $definition = include PAD . "subs/graphs/types/$kind.php";
      foreach ( $options as $key => $value )
        if ( ! in_array ( $key, $definition ['options'], TRUE ) ) throw new InvalidArgumentException ( 'unknown option: ' . $key );
      return $definition ['call'] ( padGraphNormalize ( $nodes, $edges, $options, $kind ), $options );
    } catch ( InvalidArgumentException $e ) { padError ( 'padGraph: ' . $e->getMessage () ); return NULL; }
  }
?>

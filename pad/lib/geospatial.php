<?php
  function padGeo ( $operation, $a, $b = NULL, $options = [] ) {
    include_once PAD . 'subs/maps/geospatial/_lib/geospatial.php';
    try {
      if ( ! is_string ( $operation ) or ! padKind ( 'maps/geospatial', $operation ) or ! is_array ( $options ) )
        throw new InvalidArgumentException ( 'unknown operation or invalid options' );
      $definition = include PAD . "subs/maps/geospatial/types/$operation.php";
      foreach ( $options as $name => $value )
        if ( ! in_array ( $name, $definition ['options'], TRUE ) )
          throw new InvalidArgumentException ( 'unknown option: ' . $name );
      return $definition ['call'] ( $a, $b, $options );
    } catch ( InvalidArgumentException $e ) {
      padError ( 'padGeo: ' . $e->getMessage () ); return NULL;
    }
  }
?>

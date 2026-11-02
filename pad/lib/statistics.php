<?php
  function padStats ( $kind, $rows, $options = [] ) {
    include_once PAD . 'subs/statistics/_lib/statistics.php';
    try {
      if ( ! is_string ( $kind ) || ! padKind ( 'statistics', $kind ) || ! is_array ( $options ) )
        throw new InvalidArgumentException ( 'unknown kind or invalid options' );
      $definition = include PAD . "subs/statistics/types/$kind.php";
      foreach ( $options as $key => $value )
        if ( ! in_array ( $key, $definition ['options'], TRUE ) ) throw new InvalidArgumentException ( 'unknown option: ' . $key );
      $result = $definition ['call'] ( $rows, $options );
      padStatsFiniteResult ( $result );
      return $result;
    } catch ( InvalidArgumentException $e ) { padError ( 'padStats: ' . $e->getMessage () ); return NULL; }
  }
?>

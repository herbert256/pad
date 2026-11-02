<?php
  function padTimeSeries ( $kind, $rows, $options = [] ) {
    include_once PAD . 'subs/timeseries/_lib/timeseries.php';
    try {
      if ( ! is_string ( $kind ) || ! padKind ( 'timeseries', $kind ) || ! is_array ( $options ) ) throw new InvalidArgumentException ( 'unknown kind or invalid options' );
      $type = include PAD . "subs/timeseries/types/$kind.php";
      return $type ['call'] ( $rows, $options );
    } catch ( InvalidArgumentException $e ) { padError ( 'padTimeSeries: ' . $e->getMessage () ); return NULL; }
  }
?>

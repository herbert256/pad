<?php
  function padDistributionSample ( $kind, $count, $options ) {
    if ( ! is_string ( $kind ) or ! padKind ( 'fakes/distributions', $kind ) or ! is_array ( $options )
         or ! is_int ( $count ) or $count < 0 or $count > 100000 ) {
      padError ( 'padSample: expected a known distribution, count from 0 to 100000 and options array' ); return [];
    }
    $definition = include PAD . "subs/fakes/distributions/types/$kind.php";
    $parameters = [];
    foreach ( $definition ['parameters'] as $name => [ $default, $min, $max, $integer ] ) {
      $value = $options [$name] ?? $default;
      if ( ! is_numeric ( $value ) or ! is_finite ( (float) $value ) or $value < $min or $value > $max
           or ( $integer and floor ( (float) $value ) != $value ) ) {
        padError ( "padSample: invalid $name parameter for $kind" ); return [];
      }
      $parameters [$name] = $integer ? (int) $value : (float) $value;
    }
    if ( isset ( $definition ['valid'] ) and ! $definition ['valid'] ( $parameters ) ) {
      padError ( "padSample: incompatible parameters for $kind" ); return [];
    }
    if ( $count * $definition ['work'] ( $parameters ) > 10000000 ) {
      padError ( 'padSample: the requested batch exceeds the ten-million-operation budget' ); return [];
    }
    foreach ( $options as $key => $value )
      if ( ! array_key_exists ( $key, $parameters ) and ! in_array ( $key, [ 'seed', 'random' ], TRUE ) ) {
        padError ( 'padSample: unknown option' ); return [];
      }
    if ( isset ( $options ['random'] ) and ( ! is_callable ( $options ['random'] ) or isset ( $options ['seed'] ) ) ) {
      padError ( 'padSample: random must be a callable, and cannot be combined with seed' ); return [];
    }
    $stream = $options ['random'] ?? 'padFakeUnit';
    $random = function () use ( $stream ) {
      $value = $stream ();
      if ( ! is_numeric ( $value ) or ! is_finite ( (float) $value ) or $value < 0 or $value >= 1 )
        throw new UnexpectedValueException ( 'random stream must return a finite number in [0,1)' );
      return (float) $value;
    };
    $state = padFakeState ();
    if ( array_key_exists ( 'seed', $options ) ) padFakeSeed ( $options ['seed'] );
    try {
      $values = [];
      for ( $i = 0; $i < $count; $i++ ) {
        $value = $definition ['sample'] ( $parameters, $random );
        if ( ! is_finite ( (float) $value ) ) throw new UnexpectedValueException ( 'sample overflow' );
        $values [] = $value;
      }
      return $values;
    } catch ( UnexpectedValueException $e ) {
      padError ( 'padSample: ' . $e->getMessage () ); return [];
    } finally {
      if ( array_key_exists ( 'seed', $options ) ) padFakeState ( $state );
    }
  }
?>

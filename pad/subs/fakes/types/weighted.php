<?php

  // weighted - A seeded choice from explicit values and finite nonnegative weights.

  $values = padFakeOpt ( 'values', [] );
  $weights = padFakeOpt ( 'weights', [] );
  if ( ! is_array ( $values ) or ! is_array ( $weights ) or ! $values or count ( $values ) !== count ( $weights ) or count ( $values ) > 10000 ) {
    padError ( 'fake:weighted needs equally sized, nonempty values and weights arrays (at most 10000)' ); return NULL;
  }
  $values = array_values ( $values );
  $weights = array_values ( $weights );
  foreach ( $weights as $weight )
    if ( ! is_numeric ( $weight ) or ! is_finite ( (float) $weight ) or $weight < 0 ) {
      padError ( 'fake:weighted weights must be finite and nonnegative' ); return NULL;
    }
  $maximum = max ( $weights );
  if ( $maximum <= 0 ) { padError ( 'fake:weighted needs a positive total weight' ); return NULL; }
  // Scaling prevents a finite set of large weights from overflowing its sum.
  $weights = array_map ( fn ( $w ) => $w / $maximum, $weights );
  $target = padFakeUnit () * array_sum ( $weights );
  $last = NULL;
  foreach ( $weights as $i => $weight )
    if ( $weight > 0 ) {
      $last = $values [$i];
      if ( $target < $weight ) return $last;
      $target -= $weight;
    }
  return $last; // Floating-point rounding at the upper boundary.
?>

<?php
  return [
    'parameters' => [ 'mu' => [ 0, -700, 700, FALSE ], 'sigma' => [ 1, 0, 100, FALSE ] ],
    'support' => '(0,infinity)', 'work' => fn ( $p ) => 2,
    'sample' => function ( $p, $random ) {
      $z = sqrt ( -2 * log ( 1 - $random () ) ) * cos ( 2 * M_PI * $random () );
      $value = exp ( $p ['mu'] + $p ['sigma'] * $z );
      if ( ! is_finite ( $value ) || $value <= 0 ) throw new UnexpectedValueException ( 'lognormal overflow or underflow' );
      return $value;
    }
  ];
?>

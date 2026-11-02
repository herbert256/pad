<?php
  // Marsaglia-Tsang gamma rejection, at most 128 attempts per shape. The shape < 1
  // transform is kept in log space so small positive gamma samples do not underflow.
  return [
    'parameters' => [ 'alpha' => [ 1, .01, 10000, FALSE ], 'beta' => [ 1, .01, 10000, FALSE ] ],
    'support' => '[0,1]', 'work' => fn ( $p ) => 770,
    'sample' => function ( $p, $random ) {
      $gamma = function ( $shape ) use ( $random ) {
        $d = max ( 1, $shape ) + ( $shape < 1 ? $shape : 0 ) - 1 / 3;
        $c = 1 / sqrt ( 9 * $d );
        for ( $attempt = 0; $attempt < 128; $attempt++ ) {
          $x = sqrt ( -2 * log ( 1 - $random () ) ) * cos ( 2 * M_PI * $random () );
          $v = 1 + $c * $x;
          if ( $v <= 0 ) continue;
          $v **= 3; $u = 1 - $random ();
          if ( log ( $u ) < .5 * $x * $x + $d * ( 1 - $v + log ( $v ) ) )
            return log ( $d ) + log ( $v ) + ( $shape < 1 ? log ( 1 - $random () ) / $shape : 0 );
        }
        throw new UnexpectedValueException ( 'beta rejection limit exceeded' );
      };
      $a = $gamma ( $p ['alpha'] ); $b = $gamma ( $p ['beta'] );
      return $a >= $b ? 1 / ( 1 + exp ( $b - $a ) ) : exp ( $a - $b ) / ( 1 + exp ( $a - $b ) );
    }
  ];
?>

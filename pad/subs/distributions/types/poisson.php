<?php
  return [
    'parameters' => [ 'lambda' => [ 1, 0, 1000, FALSE ] ],
    'support' => 'nonnegative integers',
    'work' => fn ( $p ) => max ( 1, 2 * $p ['lambda'] ),
    'sample' => function ( $p, $random ) {
      // Add independent Poisson chunks <=20 to avoid exp(-lambda) underflow.
      $remaining = $p ['lambda']; $sum = 0; $steps = 0;
      while ( $remaining > 0 ) {
        $chunk = min ( 20, $remaining ); $threshold = exp ( -$chunk );
        $product = 1; $k = 0;
        do {
          if ( ++$steps > 10000 ) throw new UnexpectedValueException ( 'Poisson stream exceeded its bounded sampling loop' );
          $product *= 1 - $random (); $k++;
        } while ( $product > $threshold );
        $sum += $k - 1; $remaining -= $chunk;
      }
      return $sum;
    }
  ];
?>

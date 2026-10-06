<?php

  // Build strategy 'bool' for the antiprime sequence - the highly composite numbers, those
  // with strictly more divisors than every smaller number: 1, 2, 4, 6, 12, 24, 36, 48, 60,
  // 120, 180, 240, 360, ...
  //
  // Counting the divisors of every smaller number again for each candidate cost on the order
  // of n * sqrt(n) per candidate: a flag over 720720 took five seconds and {sequence
  // antiprime, from=500000, to=730000} ran into the time limit. The list is made once
  // instead, for every number an integer holds. Spreading a number's prime exponents in
  // falling order over the smallest primes neither raises it nor changes its divisor count,
  // so every record of the divisor count is a product 2^a * 3^b * 5^c ... with a >= b >= c
  // ... - there are some 44,000 of those up to PHP_INT_MAX, each with its divisor count from
  // its exponents, and walked in order the records among them are the 167 highly composite
  // numbers an integer holds. generated.php caches the first 20.
  //
  // Nothing smaller than 1 can be compared against, so the divisor count of 0 would go
  // unchallenged and 0 would be reported a member; the sequence is positive, so anything
  // under 1 is answered first.

  function pqBoolAntiprime ($n, $p=0) {

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    return isset ( pqAntiprimeList () [ (int) $n ] );

  }

  function pqAntiprimeList () {

    static $list = NULL;

    if ( $list !== NULL )
      return $list;

    $primes = [ 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53 ];
    $counts = [];

    $walk = function ( $at, $value, $count, $most ) use ( &$walk, &$counts, $primes ) {

      $counts [$value] = $count;

      if ( $at >= count ( $primes ) )
        return;

      for ( $exponent = 1; $exponent <= $most; $exponent++ ) {

        if ( $value > intdiv ( PHP_INT_MAX, $primes [$at] ) )
          break;

        $value *= $primes [$at];

        $walk ( $at + 1, $value, $count * ( $exponent + 1 ), $exponent );

      }

    };

    $walk ( 0, 1, 1, 64 );

    ksort ( $counts );

    $list = [];
    $best = 0;

    foreach ( $counts as $value => $count )
      if ( $count > $best ) {
        $best          = $count;
        $list [$value] = TRUE;
      }

    return $list;

  }

?>

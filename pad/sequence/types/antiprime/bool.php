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
  // The walk keeps the numbers and their divisor counts in two plain lists, side by side, and
  // sorts the numbers with their positions kept: keyed by the number itself, as it was, every
  // key ends in a run of zero bits - each is a multiple of a power of two, most of a high one -
  // and PHP files an integer key by its low bits, so nearly all 44,000 landed in the same few
  // buckets and every store searched a chain thousands long - 280 ms a request for a list that
  // takes 6.
  //
  // Nothing smaller than 1 can be compared against, so the divisor count of 0 would go
  // unchallenged and 0 would be reported a member; the sequence is positive, so anything
  // under 1 is answered first.
  //
  // A candidate up to the last of the 20 terms generated.php holds is answered from them: the
  // pages that show the sequence, a play or a flag over its first terms never pass 7560, and
  // need not have the list made at all.

  function pqBoolAntiprime ($n, $p=0) {

    static $first = NULL;

    if ( ! pqBoolWhole ( $n ) or $n < 1 )
      return FALSE;

    if ( $first === NULL ) {
      include_once PT . 'antiprime/generated.php';
      $first = array_fill_keys ( PADantiprime, TRUE );
    }

    if ( $n <= PADantiprime [ count ( PADantiprime ) - 1 ] )
      return isset ( $first [ (int) $n ] );

    return isset ( pqAntiprimeList () [ (int) $n ] );

  }

  // The first highly composite number at or above $n, or INF past the last one an integer
  // holds - for build/one.php, which walks antiprime from one to the next.

  function pqNextAntiprime ( $n ) {

    include_once PT . 'antiprime/generated.php';

    $list = ( $n <= PADantiprime [ count ( PADantiprime ) - 1 ] ) ? PADantiprime : array_keys ( pqAntiprimeList () );

    foreach ( $list as $value )
      if ( $value >= $n )
        return $value;

    return INF;

  }

  function pqAntiprimeList () {

    static $list = NULL;

    if ( $list !== NULL )
      return $list;

    $primes = [ 2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53 ];
    $values = [];
    $counts = [];

    $walk = function ( $at, $value, $count, $most ) use ( &$walk, &$values, &$counts, $primes ) {

      $values [] = $value;
      $counts [] = $count;

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

    asort ( $values, SORT_NUMERIC );

    $list = [];
    $best = 0;

    foreach ( $values as $at => $value )
      if ( $counts [$at] > $best ) {
        $best          = $counts [$at];
        $list [$value] = TRUE;
      }

    return $list;

  }

?>

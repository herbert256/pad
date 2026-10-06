<?php

  // Membership predicate and generation path for semiprime: pqBoolSemiprime($num) is TRUE
  // when num is the product of exactly two primes, the two allowed to be equal - 4, 6, 9,
  // 10, 14, 15, 21, 22, 25, 26, ...
  //
  // A prime is no semiprime. Otherwise the smallest prime factor is looked for up to the cube
  // root: found, num is a semiprime when what it leaves is a prime; not found, every prime
  // factor is above the cube root, so there are exactly two of them. Primality goes to
  // prime/bool.php. Counting factors by trial division up to the square root cost a billion
  // divisions for a large prime: a flag over 9223372036854775783 spent the whole time limit
  // on it. The only file in the type, so it is the generation path as well and the whole
  // range is filtered through it.

  include_once PT . 'prime/bool.php';

  function pqBoolSemiprime ($num, $p=0) {

    if ( ! pqBoolWhole ( $num ) or $num < 4 )
      return FALSE;

    $num = (int) $num;

    if ( pqBoolPrime ( $num ) )
      return FALSE;

    for ( $i = 2; $i * $i * $i <= $num; $i++ )
      if ( $num % $i == 0 )
        return pqBoolPrime ( intdiv ( $num, $i ) );

    return TRUE;

  }

?>

<?php

  // Build strategy 'bool' for the lucky sequence, meant to be the lucky-number sieve: strike
  // out every second number, then every third of what survives, and so on, leaving 1, 3, 7,
  // 9, 13, 15, 21, 25, ...
  //
  // The sieve position $counter is a plain local, so every recursive call resets it to 2
  // instead of advancing it, and only the strike-out-every-second step is ever applied. What
  // this actually accepts is 1, 3, 7, 15, 31, 63, ... - the numbers 2^k - 1 - which is also
  // what generated.php records. The algorithm it is modelled on keeps that counter static.

  function pqBoolLucky ($n, $p=0) {

    $counter = 2;

    if($counter > $n)
        return TRUE;

    $x =  (int) $n;

    if ( $x % $counter == 0 )
        return FALSE;

    $next_position = $n - ($n / $counter);

    $counter++;

    return pqBoolLucky ($next_position);

  }

?>
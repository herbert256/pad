<?php

  // Function build for newmanConway: pqNewmanConway($n) is the nth term of the
  // Newman-Conway sequence, a(1) = a(2) = 1 and a(n) = a(a(n-1)) + a(n - a(n-1)), giving
  // 1, 1, 2, 2, 3, 4, 4, 4, 5, 6, 7, 7, 8, ...
  //
  // The doubly self-referential recursion is hopeless without help, so terms are memoised
  // in $pqCache - a $pq* global, and so wiped between runs by sequence/inits/clear.php.

function pqNewmanConway ($n) {

  global $pqCache;

  if ($n == 1 || $n == 2)
    return 1;

  if ( ! isset ( $pqCache ) )
    $pqCache = [];

  if ( isset ( $pqCache [$n] ) )
    return $pqCache [$n];

  $now = pqNewmanConway ( pqNewmanConway ($n - 1)     )
         +
       pqNewmanConway ( $n - pqNewmanConway($n - 1) );

  $pqCache [$n] = $now;

  return $now;

}

?>
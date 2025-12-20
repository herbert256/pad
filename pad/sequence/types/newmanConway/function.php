<?php

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
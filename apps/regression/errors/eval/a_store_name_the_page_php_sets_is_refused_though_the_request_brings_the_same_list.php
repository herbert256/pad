<?php

  // The visitor sends ?primes[]=1, and the page's PHP sets the name from it: the array is the
  // page's own, as it is without the query, and the store is refused.

  $_GET ['primes'] = [ '1' ];

  padGetParms ( 'GET', [ 'primes' => [ '1' ] ] );

  $primes = $_GET ['primes'] ?? [];

?>

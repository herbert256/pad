<?php

  // The visitor sends ?primes[]=1 - the very list the template sets under the store's name
  // with {set}: the array is the page's own, and the store is refused as without the query.

  $_GET ['primes'] = [ '1' ];

  padGetParms ( 'GET', [ 'primes' => [ '1' ] ] );

?>

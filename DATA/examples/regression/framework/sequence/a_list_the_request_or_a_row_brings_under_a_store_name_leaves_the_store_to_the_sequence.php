<?php

  // The visitor sends ?primes[]=1&orders[]=7 - lists, under the names of the stores the page
  // pushes and names - which become variables as every request value does. They are no array
  // the page made: the stores are kept as without them, where the first ended the page on
  // "the store name 'primes' is already an array of the page" and the second kept no store.

  $_GET ['primes'] = [ '1' ];
  $_GET ['orders'] = [ '7' ];

  padGetParms ( 'GET', [ 'primes' => [ '1' ], 'orders' => [ '7' ] ] );

?>

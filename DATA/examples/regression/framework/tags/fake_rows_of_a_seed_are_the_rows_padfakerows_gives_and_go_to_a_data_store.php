<?php

  // The rows of {fake ..., rows=, seed=} are those padFakeRows gives for the seed, and an
  // email of a row with a name is that name's.

  $fromPhp = padFakeRows ( [ 'customer' => 'name', 'mail' => 'email', 'total' => [ 'price', [ 'from' => 10, 'to' => 99 ] ] ], 3, 11 );

?>

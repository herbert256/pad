<?php

  // Ten customers, the same ten every run: the fake data starts from a seed.

  padFakeSeed ( 7 );

  padFactory ( 'customers', 10, fn ( $i ) => [
    'name'  => $name = padFakeName (),
    'city'  => padFakeCity (),
    'email' => padFakeEmail ( $name ),
  ] );

?>

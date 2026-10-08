<?php

  padFakeSeed ( 3 );

  $digits = [];

  for ( $i = 0; $i < 10; $i++ )
    $digits [] = padFakeUnique ( 'digit', 'padFakeNumber', 0, 9 );

  sort ( $digits );

  padFakeUniqueReset ( 'digit' );

  $uuid = padFakeUuid ();

  $r = json_encode ( [
    'digits'   => $digits,
    'again'    => padFakeUnique ( 'digit', fn () => 7 ),
    'otherKey' => padFakeUnique ( 'other', fn () => 7 ),
    'uuid'     => (bool) preg_match ( '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid ),
  ] );

?>

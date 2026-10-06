<?php

  $token = padStrRandom ( 32 );
  $first = padStrUuid ( 7 );
  $later = padStrUuid ( 7 );
  $id    = padStrUuid ();

  // Random on every request: what is shown is their shape,
  // not their value.

  $tokenShape = preg_match ( '/^[A-Za-z0-9]{32}$/', $token )
              ? '32 letters and digits' : 'wrong';
  $shape      = preg_replace ( '/[0-9a-f]/', 'x', $id );
  $versions   = $id [14] . ' and ' . $first [14];
  $ordered    = strcmp ( $later, $first ) > 0 ? 'yes' : 'no';

?>

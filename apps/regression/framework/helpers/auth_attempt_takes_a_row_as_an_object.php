<?php

  // padAttempt logs in a row read as an object, as padLogin does, where the right password
  // was refused without a word.

  $hash = padHash ( 'one' );
  $row  = (object) [ 'id' => '3', 'name' => 'Cid', 'password' => $hash ];
  $yes  = fn ( $b ) => $b ? 'yes' : 'no';

  $tried = $yes ( padAttempt ( 'one', $hash, $row ) ) . ' ' . padUser ( 'name' );

  padLogout ();

?>

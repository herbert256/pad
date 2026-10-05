<?php

  // padFlashInput keeps the input for the next request - passwords and the CSRF token left
  // out unless other names are given - and padOld reads it, by dot path, with a default for
  // what is not there. In the request that flashed it, padOld reads it already.

  $_POST = [ 'email' => ' ann@example.com ', 'password' => 'secret', 'password_confirmation' => 'secret',
             'padCsrfToken' => 'token', 'address' => [ 'city' => 'Delft' ] ];

  $o1 = ( padFlashInput () ? 'kept' : 'not kept' ) . ' | ' . padOld ( 'email' ) . ' | ' . padOld ( 'address.city' )
      . ' | ' . padOld ( 'password', 'no password' ) . ' | ' . padOld ( 'padCsrfToken', 'no token' )
      . ' | ' . padOld ( 'phone' ) . '/' . padOld ( 'phone', 'no phone' );

  padFlashInput ( 'email, address, padInclude' );

  $o2 = json_encode ( padOld () );

?>

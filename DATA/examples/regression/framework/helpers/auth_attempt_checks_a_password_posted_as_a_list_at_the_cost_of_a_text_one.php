<?php

  // A password posted as a list - password[]=x - matches nothing, but padAttempt checks it
  // against the user's hash all the same: refused before any hash was checked, a known name
  // answered at once where an unknown one spent a whole bcrypt on the stand-in hash, and one
  // request told which names exist. Timed against a wrong text password on the same hash.

  $hash = password_hash ( 'secret', PASSWORD_BCRYPT, [ 'cost' => 10 ] );
  $row  = [ 'id' => 1, 'name' => 'ann', 'password' => $hash ];

  $verify = PHP_INT_MAX;

  for ( $try = 0; $try < 2; $try++ ) {
    $from   = hrtime ( TRUE );
    password_verify ( 'wrong', $hash );
    $verify = min ( $verify, hrtime ( TRUE ) - $from );
  }

  $from = hrtime ( TRUE );
  $list = padAttempt ( [ 'x' ], $hash, $row ) ? 'in' : 'refused';
  $cost = ( hrtime ( TRUE ) - $from > $verify / 3 ) ? 'the cost of a text one' : 'no cost at all';

?>

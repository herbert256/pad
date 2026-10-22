<?php

  // A second padFakeSeed in a request restarts the sequence, not the unique keys: two
  // seeders that each start with padFakeSeed ( 42 ) and fill a unique column get values that
  // never collide. padFakeUniqueReset () is the way to the very same values again.

  $run = function () {
    padFakeSeed ( 42 );
    return [ padFakeName (), padFakeUnique ( 'email', 'padFakeEmail' ), padFakeUnique ( 'email', 'padFakeEmail' ) ];
  };

  $first  = $run ();
  $second = $run ();

  padFakeUniqueReset ();

  $third = $run ();

  $r = json_encode ( [ 'sequence' => $first [0] === $second [0],
                       'unique'   => count ( array_unique ( [ $first [1], $first [2], $second [1], $second [2] ] ) ) == 4,
                       'reset'    => $first === $third ] );

?>

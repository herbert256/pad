<?php

  // The same seed twice in a request gives the same values twice - padFakeUnique's too: a
  // seed is a new start, and the values it gave before belong to the sequence that made them.

  $run = function () {
    padFakeSeed ( 42 );
    return [ padFakeName (), padFakeUnique ( 'person', 'padFakeName' ), padFakeUnique ( 'person', 'padFakeName' ) ];
  };

  $first  = $run ();
  $second = $run ();

  $r = json_encode ( [ 'same' => $first === $second, 'twice' => $first [1] === $first [2] ] );

?>

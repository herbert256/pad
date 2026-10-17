<?php

  // A generator that yields from two lists gives the second list's keys 0, 1 again; read by
  // key, its rows overwrote the first list's and four rows read as two. A key that comes twice
  // reads the items as a list; distinct keys stay as they were.

  function padCaseRowsTwice () {
    yield from [ [ 'n' => 1 ], [ 'n' => 2 ] ];
    yield from [ [ 'n' => 3 ], [ 'n' => 4 ] ];
  }

  function padCaseRowsKeyed () {
    yield 'a' => [ 'n' => 5 ];
    yield 'b' => [ 'n' => 6 ];
  }

  $rows  = padCaseRowsTwice ();
  $keyed = padCaseRowsKeyed ();

?>

<?php

  // A sort before first= and one after it, a sort before a group and one after it: each
  // sorts on its own value - both sorted on the last one written.

  $xs     = [ [ 'n' => 3 ], [ 'n' => 1 ], [ 'n' => 2 ], [ 'n' => 5 ], [ 'n' => 4 ] ];
  $orders = [ [ 'c' => 'a', 't' => 1, 'd' => '2026-03' ], [ 'c' => 'b', 't' => 9, 'd' => '2026-01' ], [ 'c' => 'a', 't' => 5, 'd' => '2026-02' ],
              [ 'c' => 'a', 't' => 2, 'd' => '2026-01' ], [ 'c' => 'b', 't' => 1, 'd' => '2026-02' ] ];

?>

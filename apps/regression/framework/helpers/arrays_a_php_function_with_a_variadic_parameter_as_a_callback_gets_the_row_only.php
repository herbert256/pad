<?php

  // One of PHP's own functions given as a callback is handed the row only - it would read
  // the key as an argument of its own. A variadic one, max(...) or array_merge(...), was let
  // through with the key as a second value: max compared each row with its key and answered
  // the row, and array_merge refused the key as no array.

  $scores = [ 'ann' => [ 3, 9, 4 ], 'bob' => [ 7, 2 ] ];

  $r = json_encode ( [
    'pluck' => padArrPluck ( $scores, max ( ... ) ),
    'sum'   => padArrSum ( $scores, max ( ... ) ),
    'sort'  => array_keys ( padArrSortBy ( $scores, min ( ... ) ) ),
    'group' => array_keys ( padArrGroupBy ( [ 'x' => [ 1, 5 ], 'y' => [ 5, 2 ], 'z' => [ 3 ] ], max ( ... ) ) ),
    'merge' => padArrPluck ( [ [ 'a' => 1 ] ], array_merge ( ... ) ),
    'first' => padArrFirst ( [ 'p' => [ 1, 2 ], 'q' => [ 6, 1 ] ], fn ( $row ) => max ( $row ) > 5 )
  ] );

  unset ( $scores );

?>

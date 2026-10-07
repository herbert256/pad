<?php

  // A NAN made into a key or read as text is NAN, as INF is INF and as the string helpers
  // read it: cast with (string), PHP 8.5's "unexpected NAN value was coerced to string"
  // ended the request in padArrKeyBy, padArrGroupBy, padArrPluck, padArrDot and like.

  $rows = [ [ 'id' => fdiv ( 0, 0 ), 'n' => 'a' ], [ 'id' => 2, 'n' => 'b' ], [ 'id' => INF, 'n' => 'c' ] ];

  $r = json_encode ( [
    array_keys ( padArrKeyBy ( $rows, 'id' ) ),
    array_keys ( padArrGroupBy ( $rows, 'id' ) ),
    padArrPluck ( $rows, 'n', 'id' ),
    array_keys ( padArrDot ( [ 'x' => 1 ], fdiv ( 0, 0 ) ) ),
    array_keys ( padArrWhere ( [ 'p' => [ 'v' => fdiv ( 0, 0 ) ], 'q' => [ 'v' => 'nanny' ] ], 'v', 'like', 'NAN%' ) ),
    array_keys ( padArrWhere ( [ 'p' => [ 'v' => 'NaN' ], 'q' => [ 'v' => 'x' ] ], 'v', 'like', fdiv ( 0, 0 ) ) )
  ] );

  unset ( $rows );

?>

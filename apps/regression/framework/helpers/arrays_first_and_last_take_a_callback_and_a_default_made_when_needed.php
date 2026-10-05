<?php

  $numbers = [ 'a' => 1, 'b' => 5, 'c' => 8 ];

  $r = json_encode ( [
    'first'        => padArrFirst ( $numbers ),
    'firstOver3'   => padArrFirst ( $numbers, fn ( $value ) => $value > 3 ),
    'firstByKey'   => padArrFirst ( $numbers, fn ( $value, $key ) => $key == 'c' ),
    'firstString'  => padArrFirst ( [ 'x', '5' ], 'is_numeric' ),
    'firstEmpty'   => padArrFirst ( [], NULL, 'none' ),
    'firstNone'    => padArrFirst ( $numbers, fn ( $value ) => $value > 9, fn () => 'made' ),
    'firstNull'    => padArrFirst ( [ NULL, 1 ], NULL, 'not used' ),
    'firstOnNull'  => padArrFirst ( NULL ),
    'last'         => padArrLast ( $numbers ),
    'lastUnder8'   => padArrLast ( $numbers, fn ( $value ) => $value < 8 ),
    'lastEmpty'    => padArrLast ( '', NULL, 'none' ),
    'lastObject'   => padArrLast ( new ArrayObject ( [ 'p', 'q' ] ) )
  ] );

?>

<?php

  $r = json_encode ( [
    'flatten'      => padArrFlatten ( [ 1, [ 2, [ 3, [ 4 ] ] ], 'k' => [ 'x' => 5 ] ] ),
    'depthOne'     => padArrFlatten ( [ 1, [ 2, [ 3, [ 4 ] ] ] ], 1 ),
    'depthZero'    => padArrFlatten ( [ 'a' => 1, [ 2 ] ], 0 ),
    'emptyInside'  => padArrFlatten ( [ [], [ [] ], 0, '', NULL ] ),
    'flattenNull'  => padArrFlatten ( NULL ),
    'dot'          => padArrDot ( [ 'a' => [ 'b' => 1, 'c' => [ 'd' => 2 ] ], 'e' => [], 'l' => [ 'x', 'y' ] ] ),
    'prepend'      => padArrDot ( [ 'mail' => [ 'from' => 'shop' ] ], 'config.' ),
    'undot'        => padArrUndot ( [ 'a.b' => 1, 'a.c.d' => 2, 'l.0' => 'x', 'l.1' => 'y', 's.*' => 'a star' ] ),
    'roundTrip'    => padArrUndot ( padArrDot ( [ 'a' => [ 'b' => [ 'c' => 1 ] ], 'n' => [ 1, 2 ] ] ) ),
    'wrapNull'     => padArrWrap ( NULL ),
    'wrapArray'    => padArrWrap ( [ 'a' => 1 ] ),
    'wrapText'     => padArrWrap ( 'x' ),
    'wrapZero'     => padArrWrap ( 0 ),
    'wrapEmpty'    => padArrWrap ( '' ),
    'wrapFalse'    => padArrWrap ( FALSE )
  ] );

?>

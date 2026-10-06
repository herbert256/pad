<?php

  $orders = [
    [ 'total' => '150' ],
    [ 'total' => 80 ],
    [ 'total' => 12.5 ],
    [ 'total' => NULL ],
    [ 'total' => 'n/a' ],
    [ 'other' => 1 ]
  ];

  $r = json_encode ( [
    'sum'        => padArrSum ( $orders, 'total' ),
    'avg'        => padArrAvg ( $orders, 'total' ),
    'min'        => padArrMin ( $orders, 'total' ),
    'max'        => padArrMax ( $orders, 'total' ),
    'values'     => padArrSum ( [ 1, '2', TRUE, ' 3', [ 4 ] ] ),
    'callback'   => padArrMax ( [ 'bb', 'a', 'cccc' ], fn ( $value ) => strlen ( $value ) ),
    'sumNone'    => padArrSum ( [] ),
    'avgNone'    => padArrAvg ( [ 'x' ] ),
    'minNone'    => padArrMin ( NULL ),
    'maxNone'    => padArrMax ( '' ),
    'avgInts'    => padArrAvg ( [ 2, 4 ] ),
    'avgFloat'   => padArrAvg ( [ 1, 2 ] )
  ], JSON_PRESERVE_ZERO_FRACTION );

?>

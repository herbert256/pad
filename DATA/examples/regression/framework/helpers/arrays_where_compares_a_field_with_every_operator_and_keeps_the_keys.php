<?php

  $orders = [
    'a' => [ 'n' => 1, 'status' => 'paid', 'total' => '150' ],
    'b' => [ 'n' => 2, 'status' => 'open', 'total' => 80 ],
    'c' => [ 'n' => 3, 'status' => 'Paid', 'total' => 100 ]
  ];

  $keys = function ( $rows ) { return implode ( ',', array_keys ( $rows ) ); };

  $r = json_encode ( [
    'two'      => $keys ( padArrWhere ( $orders, 'status', 'paid' ) ),
    '='        => $keys ( padArrWhere ( $orders, 'total', '=', 100 ) ),
    '=='       => $keys ( padArrWhere ( $orders, 'total', '==', '150' ) ),
    '==='      => $keys ( padArrWhere ( $orders, 'total', '===', 100 ) ),
    '!='       => $keys ( padArrWhere ( $orders, 'status', '!=', 'paid' ) ),
    '<>'       => $keys ( padArrWhere ( $orders, 'n', '<>', 2 ) ),
    '!=='      => $keys ( padArrWhere ( $orders, 'total', '!==', 100 ) ),
    '<'        => $keys ( padArrWhere ( $orders, 'total', '<', 100 ) ),
    '>'        => $keys ( padArrWhere ( $orders, 'total', '>', 90 ) ),
    '<='       => $keys ( padArrWhere ( $orders, 'total', '<=', 100 ) ),
    '>='       => $keys ( padArrWhere ( $orders, 'total', '>=', 100 ) ),
    'gt'       => $keys ( padArrWhere ( $orders, 'total', 'GT', 90 ) ),
    'in'       => $keys ( padArrWhere ( $orders, 'n', 'in', [ '1', 3 ] ) ),
    'not in'   => $keys ( padArrWhere ( $orders, 'n', 'Not In', [ 1, 3 ] ) ),
    'missing'  => $keys ( padArrWhere ( $orders, 'note', NULL ) ),
    'objects'  => $keys ( padArrWhere ( [ 'x' => [ 'v' => new stdClass ], 'y' => [ 'v' => 1 ] ], 'v', '<', 5 ) ),
    'none'     => $keys ( padArrWhere ( NULL, 'n', 1 ) )
  ] );

  unset ( $keys );

?>

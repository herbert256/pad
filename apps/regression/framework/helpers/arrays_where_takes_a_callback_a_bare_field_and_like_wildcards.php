<?php

  $orders = [
    'a' => [ 'n' => 1, 'status' => 'paid', 'note' => 'Rush, 50% off', 'gift' => 1 ],
    'b' => [ 'n' => 2, 'status' => 'open', 'note' => 'Ärger',         'gift' => 0 ],
    'c' => [ 'n' => 3, 'status' => 'Paid', 'note' => NULL,            'gift' => 'yes' ],
    'd' => [ 'n' => 4, 'status' => 'pending' ]
  ];

  $keys = function ( $rows ) { return implode ( ',', array_keys ( $rows ) ); };

  $r = json_encode ( [
    'callback' => $keys ( padArrWhere ( $orders, fn ( $row, $key ) => $row ['n'] > 1 and $key != 'd' ) ),
    'bare'     => $keys ( padArrWhere ( $orders, 'gift' ) ),
    'like'     => $keys ( padArrWhere ( $orders, 'status', 'like', 'pa%' ) ),
    'one'      => $keys ( padArrWhere ( $orders, 'status', 'like', '_pen' ) ),
    'escaped'  => $keys ( padArrWhere ( $orders, 'note', 'like', '%50\\% off' ) ),
    'unicode'  => $keys ( padArrWhere ( $orders, 'note', 'like', 'ä%' ) ),
    'anything' => $keys ( padArrWhere ( $orders, 'note', 'like', '%' ) ),
    'php'      => $keys ( padArrWhere ( [ 'a' => 'x', 'b' => '5', 'c' => 7 ], is_numeric ( ... ) ) )
  ] );

  unset ( $keys );

?>

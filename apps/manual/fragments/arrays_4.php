<?php

  $orders = [
    [ 'number' => 101, 'customer' => 'Ann',  'status' => 'paid', 'total' => '120.00' ],
    [ 'number' => 102, 'customer' => 'Bob',  'status' => 'open', 'total' => '35.50'  ],
    [ 'number' => 103, 'customer' => 'Cleo', 'status' => 'paid', 'total' => '89.90'  ],
    [ 'number' => 104, 'customer' => 'Carl', 'status' => 'open', 'total' => '15.00'  ]
  ];

  $large  = padArrSortBy ( padArrWhere ( $orders, 'total', '>=', 50 ), 'total', 'desc' );
  $withC  = padArrWhere ( $orders, 'customer', 'like', 'c%' );
  $unpaid = padArrWhere ( $orders, 'status', 'open' );

  $firstOpen = padArrFirst ( $orders, fn ( $order ) => $order ['status'] == 'open' );
  $lastPaid  = padArrLast  ( $orders, fn ( $order ) => $order ['status'] == 'paid' );

  $firstOpenNumber = $firstOpen ['number'];
  $lastPaidNumber  = $lastPaid ['number'];

?>

<?php

  $sales = [
    [ 'region' => 'North', 'amount' => '120' ],
    [ 'region' => 'South', 'amount' => '80'  ],
    [ 'region' => 'North', 'amount' => '200' ],
    [ 'region' => 'East',  'amount' => NULL  ],
    [ 'region' => 'South', 'amount' => '40'  ]
  ];

  $regions = [];

  foreach ( padArrGroupBy ( $sales, 'region' ) as $region => $rows )
    $regions [] = [
      'region' => $region,
      'sales'  => count ( $rows ),
      'sum'    => padArrSum ( $rows, 'amount' ),
      'best'   => padArrMax ( $rows, 'amount' ) ?? '-'
    ];

  $grandTotal = padArrSum ( $sales, 'amount' );
  $average    = padArrAvg ( $sales, 'amount' );
  $smallest   = padArrMin ( $sales, 'amount' );

?>

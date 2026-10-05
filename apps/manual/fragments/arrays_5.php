<?php

  $sales = [
    [ 'region' => 'North', 'amount' => '120' ],
    [ 'region' => 'South', 'amount' => '80'  ],
    [ 'region' => 'North', 'amount' => '200' ],
    [ 'region' => 'East',  'amount' => NULL  ],
    [ 'region' => 'South', 'amount' => '40'  ]
  ];

  $groups  = padArrGroupBy ( $sales, 'region' );
  $regions = [];

  foreach ( $groups as $region => $rows )
    $regions [] = [
      'region' => $region,
      'sales'  => count ( $rows ),
      'total'  => padArrSum ( $rows, 'amount' ),
      'best'   => padArrMax ( $rows, 'amount' ) ?? '-'
    ];

  $sum = padArrSum ( $sales, 'amount' );
  $avg = padArrAvg ( $sales, 'amount' );
  $min = padArrMin ( $sales, 'amount' );

?>

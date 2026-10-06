<?php

  $orders = [
    [ 'n' => 1, 'status' => 'paid', 'customer' => [ 'country' => 'NL' ] ],
    [ 'n' => 2, 'status' => 'open', 'customer' => [ 'country' => 'BE' ] ],
    [ 'n' => 3, 'status' => 'paid', 'customer' => [ 'country' => 'NL' ] ],
    [ 'n' => 4, 'status' => NULL ]
  ];

  $numbers = fn ( $groups ) => array_map ( fn ( $rows ) => implode ( ',', array_column ( $rows, 'n' ) ), $groups );

  $r = json_encode ( [
    'byStatus'  => $numbers ( padArrGroupBy ( $orders, 'status' ) ),
    'byCountry' => $numbers ( padArrGroupBy ( $orders, 'customer.country' ) ),
    'byParity'  => padArrGroupBy ( [ 1, 2, 3, 4 ], fn ( $value ) => $value % 2 ? 'odd' : 'even' ),
    'byValue'   => padArrGroupBy ( [ 'a', 'b', 'a' ], NULL ),
    'byNothing' => padArrGroupBy ( NULL, 'status' ),
    'keyBy'     => array_map ( fn ( $row ) => $row ['n'], padArrKeyBy ( $orders, 'n' ) ),
    'laterWins' => padArrKeyBy ( [ [ 'k' => 'a', 'v' => 1 ], [ 'k' => 'b', 'v' => 2 ], [ 'k' => 'a', 'v' => 3 ] ], 'k' ),
    'keyByMade' => array_keys ( padArrKeyBy ( $orders, fn ( $row, $key ) => "row$key" ) )
  ] );

  unset ( $numbers );

?>

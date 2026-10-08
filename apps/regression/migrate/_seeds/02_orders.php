<?php

  // Two orders for each of the first three customers, through plain db ().

  foreach ( [ 1, 2, 3 ] as $customer )
    foreach ( [ 40, 150 ] as $total )
      db ( "insert into orders (customer_id, total, placed) values ({0}, {1}, {2})",
           [ $customer, $total + $customer, '2026-10-0' . $customer ] );

?>

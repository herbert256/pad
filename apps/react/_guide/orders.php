<?php

  // orders.php - the first data of the component

  $orders = db ( "ARRAY id, customer, total, status FROM orders ORDER BY id DESC" );
  $filter = [ 'status' => 'open' ];

?>

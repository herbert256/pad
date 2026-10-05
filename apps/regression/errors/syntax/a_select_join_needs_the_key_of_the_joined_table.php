<?php

  // The join meets key= with the declared key of the joined table - and customers is not
  // declared, so there is no key for orders.customerNumber to meet.

  $padSelect ['orderCust'] = [ 'db' => 'orders', 'join' => [ 'inner' => 'customers', 'key' => 'orders.customerNumber' ] ];

?>

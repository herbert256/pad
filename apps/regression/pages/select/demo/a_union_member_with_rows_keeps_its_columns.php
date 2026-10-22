<?php

  // A union member with rows= of its own whose columns repeat a name - orders joined to
  // customers, both with customerNumber - keeps its rows and its columns: on MySQL it is a
  // member in parentheses. As a derived table it ended on "Duplicate column name
  // 'customerNumber'". Over the Classic Models tables of the demo database.

  $padSelect ['customers']   = [ 'key' => 'customerNumber' ];
  $padSelect ['firstOrders'] = [ 'db' => 'orders', 'join' => [ 'inner' => 'customers', 'key' => 'orders.customerNumber' ] ];
  $padSelect ['lastOrder']   = [ 'base' => 'firstOrders', 'order' => 'orders.orderNumber desc', 'rows' => 1 ];

?>

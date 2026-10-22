<?php

  // A select with a join meets its key and its relations with its own table's columns: a
  // field both tables hold - orderNumber in orders and orderdetails, customerNumber in orders
  // and payments - is named with the table. Over the Classic Models tables of the demo
  // database.

  $padSelect ['customers']  = [ 'key' => 'customerNumber', 'order' => 'customerName' ];
  $padSelect ['orders']     = [ 'key' => 'orderNumber' ];
  $padSelect ['lineDetail'] = [ 'db' => 'orderdetails', 'key' => 'orderNumber', 'fields' => [ 'productCode' => 'product' ] ];
  $padSelect ['orderLines'] = [ 'db' => 'orders', 'key' => 'orderNumber', 'fields' => 'orders.orderNumber',
                                'join' => [ 'inner' => 'lineDetail', 'key' => 'orders.orderNumber' ],
                                'order' => 'orderdetails.orderLineNumber' ];

  $padRelations ['orders'] ['customers'] = 'customerNumber';

?>

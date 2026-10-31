<?php

  // A table declared in backticks - the way to name one that is a reserved word - is joined
  // under that name: the ON quoted it again, ```customers```, and the join ended on "Unknown
  // column". Over the Classic Models tables of the demo database.

  $padSelect ['customerQ'] = [ 'db' => '`customers`', 'key' => 'customerNumber', 'fields' => [ 'customerName' => 'customer' ] ];
  $padSelect ['orderQ']    = [ 'db' => 'orders', 'fields' => 'orders.orderNumber',
                               'join' => [ 'inner' => 'customerQ', 'key' => 'orders.customerNumber' ] ];

?>

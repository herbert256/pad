<?php

  // A select with a join names a key or relation field with its own table only when that
  // table holds it: a field of the joined table - salesRepEmployeeNumber of customers,
  // beside orders - stays bare, where it was named orders.salesRepEmployeeNumber and the
  // select ended on "Unknown column", as a relation and as a key bound on the tag. A field
  // both hold, customerNumber, is still named with the own table. Over the Classic Models
  // tables of the demo database.

  $padSelect ['repSel']         = [ 'db' => 'employees', 'key' => 'employeeNumber', 'order' => 'employeeNumber' ];
  $padSelect ['customers']      = [ 'key' => 'customerNumber' ];
  $padSelect ['orderCustomers'] = [ 'db' => 'orders', 'key' => 'orderNumber', 'order' => 'orderNumber',
                                    'join' => [ 'inner' => 'customers', 'key' => 'orders.customerNumber' ] ];
  $padSelect ['repOrders']      = [ 'base' => 'orderCustomers', 'key' => 'salesRepEmployeeNumber' ];
  $padSelect ['customerOrders'] = [ 'base' => 'orderCustomers', 'key' => 'customerNumber' ];

  $padRelations ['orderCustomers'] ['repSel'] = [ 'salesRepEmployeeNumber' => 'employeeNumber' ];

?>

<?php

  $padSelect ['customers'] = [ 'db' => 'customers',    'key' => 'customerNumber', 'order' => 'customerName'];
  $padSelect ['employees'] = [ 'db' => 'employees',    'key' => 'employeeNumber', 'order' => 'lastName,firstName'];
  $padSelect ['offices']   = [ 'db' => 'offices',      'key' => 'officeCode'];
  $padSelect ['details']   = [ 'db' => 'orderdetails', 'key' => 'orderNumber,productCode', 'order' => 'orderLineNumber'];
  $padSelect ['orders']    = [ 'db' => 'orders',       'key' => 'orderNumber'];
  $padSelect ['payments']  = [ 'db' => 'payments',     'key' => 'customerNumber,checkNumber'];
  $padSelect ['lines']     = [ 'db' => 'productlines', 'key' => 'productLine'];
  $padSelect ['products']  = [ 'db' => 'products',     'key' => 'productCode'];

  $padRelations ['products']  ['lines']      = 'productLine';
  $padRelations ['payments']  ['customers']  = 'customerNumber';
  $padRelations ['orders']    ['customers']  = 'customerNumber';
  $padRelations ['details']   ['orders']     = 'orderNumber';
  $padRelations ['details']   ['products']   = 'productCode';
  $padRelations ['employees'] ['offices']    = 'officeCode';
  $padRelations ['customers'] ['employees']  = 'salesRepEmployeeNumber';

  $padRelations ['customers'] ['sales']      = [ 'table' => 'employees', 'key' => 'salesRepEmployeeNumber' ];
  $padRelations ['employees'] ['managers']   = [ 'table' => 'employees', 'key' => 'reportsTo'              ];
  $padRelations ['managers']  ['bosses']     = [ 'table' => 'employees', 'key' => 'reportsTo'              ];

 ?>
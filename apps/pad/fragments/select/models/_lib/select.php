<?php
 
  $padSelect ['customers'] = [ 'db' => 'customers',    'key' => 'customerNumber', 'order' => 'customerName'];
  $padSelect ['employees'] = [ 'db' => 'employees',    'key' => 'employeeNumber', 'order' => 'lastName,firstName'];
  $padSelect ['offices']   = [ 'db' => 'offices',      'key' => 'officeCode'];
  $padSelect ['details']   = [ 'db' => 'orderdetails', 'key' => 'orderNumber,productCode', 'order' => 'orderLineNumber'];
  $padSelect ['orders']    = [ 'db' => 'orders',       'key' => 'orderNumber'];
  $padSelect ['payments']  = [ 'db' => 'payments',     'key' => 'customerNumber,checkNumber'];
  $padSelect ['lines']     = [ 'db' => 'productlines', 'key' => 'productLine'];
  $padSelect ['products']  = [ 'db' => 'products',     'key' => 'productCode'];

  // Relations between PAD tables, the key of *relation* must be in $padSelect
  // $padRelations [*base*]  [*relation*]    = [];

  $padRelations ['products']  ['lines']      = [];
  $padRelations ['payments']  ['customers']  = [];
  $padRelations ['orders']    ['customers']  = [];
  $padRelations ['details']   ['orders']     = [];
  $padRelations ['details']   ['products']   = [];
  $padRelations ['employees'] ['offices']    = [];

  // Relations between PAD tables, the key of *relation* is an other field in *base*
  // $padRelations [*base*]  [*relation*] = [ 'key' => *keyField*];
  //
  // All keys of *relation* must be given, use 'n/a' if it does not exists in *base* for a partly relation

  $padRelations ['customers'] ['employees']  = [ 'key' => 'salesRepEmployeeNumber'];

  // Virtual tables

  $padRelations ['customers'] ['sales']      = [ 'table' => 'employees', 'key' => 'salesRepEmployeeNumber' ];
  $padRelations ['employees'] ['managers']   = [ 'table' => 'employees', 'key' => 'reportsTo'              ];
  $padRelations ['managers']  ['bosses']     = [ 'table' => 'employees', 'key' => 'reportsTo'              ];

 ?>
<?php
  
  $padTables ['customers'] = [ 'db' => 'customers',    'key' => 'customerNumber', 'order' => 'customerName'];
  $padTables ['employees'] = [ 'db' => 'employees',    'key' => 'employeeNumber', 'order' => 'lastName,firstName'];
  $padTables ['offices']   = [ 'db' => 'offices',      'key' => 'officeCode'];
  $padTables ['details']   = [ 'db' => 'orderdetails', 'key' => 'orderNumber,productCode', 'order' => 'orderLineNumber'];
  $padTables ['orders']    = [ 'db' => 'orders',       'key' => 'orderNumber'];
  $padTables ['payments']  = [ 'db' => 'payments',     'key' => 'customerNumber,checkNumber'];
  $padTables ['lines']     = [ 'db' => 'productlines', 'key' => 'productLine'];
  $padTables ['products']  = [ 'db' => 'products',     'key' => 'productCode'];

  // Relations between PAD tables, the key of *relation* must be in $padTables
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
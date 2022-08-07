<?php

  // $pDb_tables
  
  $pDb_tables ['customers'] = [ 'db' => 'customers',    'key' => 'customerNumber', 'order' => 'customerName'];
  $pDb_tables ['employees'] = [ 'db' => 'employees',    'key' => 'employeeNumber', 'order' => 'lastName,firstName'];
  $pDb_tables ['offices']   = [ 'db' => 'offices',      'key' => 'officeCode'];
  $pDb_tables ['details']   = [ 'db' => 'orderdetails', 'key' => 'orderNumber,productCode', 'order' => 'orderLineNumber'];
  $pDb_tables ['orders']    = [ 'db' => 'orders',       'key' => 'orderNumber'];
  $pDb_tables ['payments']  = [ 'db' => 'payments',     'key' => 'customerNumber,checkNumber'];
  $pDb_tables ['lines']     = [ 'db' => 'productlines', 'key' => 'productLine'];
  $pDb_tables ['products']  = [ 'db' => 'products',     'key' => 'productCode'];

  // Relations between PAD tables, the key of *relation* must be in $pDb_tables
  // $pDb_relations [*base*]  [*relation*]    = [];

  $pDb_relations ['products']  ['lines']      = [];
  $pDb_relations ['payments']  ['customers']  = [];
  $pDb_relations ['orders']    ['customers']  = [];
  $pDb_relations ['details']   ['orders']     = [];
  $pDb_relations ['details']   ['products']   = [];
  $pDb_relations ['employees'] ['offices']    = [];

  // Relations between PAD tables, the key of *relation* is an other field in *base*
  // $pDb_relations [*base*]  [*relation*]    = [ 'key' => *keyField*];
  //
  // All keys of *relation* must be given, use 'n/a' if it does not exists in *base* for a partly relation

  $pDb_relations ['customers'] ['employees']  = [ 'key' => 'salesRepEmployeeNumber'];
  
  // Virtual tables
  
  $pDb_relations ['customers'] ['sales']      = [ 'table' => 'employees', 'key' => 'salesRepEmployeeNumber' ];
  $pDb_relations ['employees'] ['managers']   = [ 'table' => 'employees', 'key' => 'reportsTo'              ];
  $pDb_relations ['managers']  ['bosses']     = [ 'table' => 'employees', 'key' => 'reportsTo'              ];
  $pDb_relations ['bosses']    ['hell']       = [ 'table' => 'offices',                                     ];
  
?>
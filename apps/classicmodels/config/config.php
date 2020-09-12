<?php

  // SQL parms - application

  $pad_sql_host      = '127.0.0.1';
  $pad_sql_database  = 'classicmodels';
  $pad_sql_user      = 'classicmodels';
  $pad_sql_password  = 'classicmodels';

  // $pad_db_tables
  
  $pad_db_tables ['customers'] = [ 'db' => 'customers',    'key' => 'customerNumber', 'order' => 'customerName'];
  $pad_db_tables ['employees'] = [ 'db' => 'employees',    'key' => 'employeeNumber', 'order' => 'lastName,firstName'];
  $pad_db_tables ['offices']   = [ 'db' => 'offices',      'key' => 'officeCode'];
  $pad_db_tables ['details']   = [ 'db' => 'orderdetails', 'key' => 'orderNumber,productCode', 'order' => 'orderLineNumber'];
  $pad_db_tables ['orders']    = [ 'db' => 'orders',       'key' => 'orderNumber'];
  $pad_db_tables ['payments']  = [ 'db' => 'payments',     'key' => 'customerNumber,checkNumber'];
  $pad_db_tables ['lines']     = [ 'db' => 'productlines', 'key' => 'productLine'];
  $pad_db_tables ['products']  = [ 'db' => 'products',     'key' => 'productCode'];

  // Relations between PAD tables, the key of *relation* must be in $pad_db_tables
  // $pad_db_relations [*base*]  [*relation*]    = [];

  $pad_db_relations ['products']  ['lines']      = [];
  $pad_db_relations ['payments']  ['customers']  = [];
  $pad_db_relations ['orders']    ['customers']  = [];
  $pad_db_relations ['details']   ['orders']     = [];
  $pad_db_relations ['details']   ['products']   = [];
  $pad_db_relations ['employees'] ['offices']    = [];

  // Relations between PAD tables, the key of *relation* is an other field in *base*
  // $pad_db_relations [*base*]  [*relation*]    = [ 'key' => *keyField*];
  //
  // All keys of *relation* must be given, use 'n/a' if it does not exists in *base* for a partly relation

  $pad_db_relations ['customers'] ['employees']  = [ 'key' => 'salesRepEmployeeNumber'];
  
  // Virtual tables
  
  $pad_db_relations ['customers'] ['sales']      = [ 'table' => 'employees', 'key' => 'salesRepEmployeeNumber' ];
  $pad_db_relations ['employees'] ['managers']   = [ 'table' => 'employees', 'key' => 'reportsTo'              ];
  $pad_db_relations ['managers']  ['bosses']     = [ 'table' => 'employees', 'key' => 'reportsTo'              ];
  $pad_db_relations ['bosses']    ['hell']       = [ 'table' => 'offices',                                     ];
  
?>
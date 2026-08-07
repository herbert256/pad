<?php

  // The whole of this application is this file: the tables, what identifies a row of each, and
  // how they reach one another. Every page beside it is a template that walks those relations
  // without writing a join.
  //
  // Recovered from commit ac148cccd, where it declared its tables in $padTables and gave each
  // relation an empty array. The select subsystem reads $padSelect now, and a relation names
  // the field it is followed by, so it is written in that form here.

  $padSelect ['customers'] = [ 'db' => 'customers',    'key' => 'customerNumber',          'order' => 'customerName'      ];
  $padSelect ['employees'] = [ 'db' => 'employees',    'key' => 'employeeNumber',          'order' => 'lastName,firstName'];
  $padSelect ['offices']   = [ 'db' => 'offices',      'key' => 'officeCode'                                              ];
  $padSelect ['details']   = [ 'db' => 'orderdetails', 'key' => 'orderNumber,productCode', 'order' => 'orderLineNumber'   ];
  $padSelect ['orders']    = [ 'db' => 'orders',       'key' => 'orderNumber'                                             ];
  $padSelect ['payments']  = [ 'db' => 'payments',     'key' => 'customerNumber,checkNumber'                              ];
  $padSelect ['lines']     = [ 'db' => 'productlines', 'key' => 'productLine'                                             ];
  $padSelect ['products']  = [ 'db' => 'products',     'key' => 'productCode'                                             ];

  // A relation is the field of the base table that holds the key of the other one.

  $padRelations ['products']  ['lines']     = 'productLine';
  $padRelations ['payments']  ['customers'] = 'customerNumber';
  $padRelations ['orders']    ['customers'] = 'customerNumber';
  $padRelations ['details']   ['orders']    = 'orderNumber';
  $padRelations ['details']   ['products']  = 'productCode';
  $padRelations ['employees'] ['offices']   = 'officeCode';
  $padRelations ['customers'] ['employees'] = 'salesRepEmployeeNumber';

  // Virtual tables: the employees table under another name, so that an employee has a manager
  // and a manager has a boss without any of them being a table of its own. Each is declared
  // like any other table, over the same db, and related by the field that holds the other's
  // key - an employee's reportsTo holds their manager's employeeNumber.
  //
  // The declaration used to read [ 'table' => 'employees', 'key' => 'reportsTo' ], which the
  // select subsystem no longer understands: it wants the table in $padSelect and the relation
  // as base field => related field. Written the old way the relation was silently ignored and
  // every employee came out as their own manager.

  $padSelect ['sales']    = [ 'db' => 'employees', 'key' => 'employeeNumber', 'order' => 'lastName,firstName' ];
  $padSelect ['managers'] = [ 'db' => 'employees', 'key' => 'employeeNumber', 'order' => 'lastName,firstName' ];
  $padSelect ['bosses']   = [ 'db' => 'employees', 'key' => 'employeeNumber', 'order' => 'lastName,firstName' ];

  $padRelations ['customers'] ['sales']    = [ 'salesRepEmployeeNumber' => 'employeeNumber' ];
  $padRelations ['employees'] ['managers'] = [ 'reportsTo'              => 'employeeNumber' ];
  $padRelations ['managers']  ['bosses']   = [ 'reportsTo'              => 'employeeNumber' ];

?>
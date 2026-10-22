<?php

  // A join to a declared table with a where - a view - joins only its rows: the customers of
  // the sales staff of the Paris office, not of every office. The view's where was left
  // out. Over the Classic Models tables of the demo database.

  $padSelect ['parisStaff']     = [ 'db' => 'employees', 'key' => 'employeeNumber', 'where' => "officeCode = '4'",
                                    'fields' => [ 'lastName' => 'rep' ] ];
  $padSelect ['parisCustomers'] = [ 'db' => 'customers', 'fields' => 'customers.customerName',
                                    'join' => [ 'inner' => 'parisStaff', 'key' => 'customers.salesRepEmployeeNumber' ],
                                    'order' => 'customers.customerName', 'rows' => 4 ];
  $padSelect ['repsLeft']       = [ 'db' => 'customers', 'fields' => 'customers.customerName',
                                    'join' => [ 'left' => 'parisStaff', 'key' => 'customers.salesRepEmployeeNumber' ],
                                    'where' => "customers.country = 'USA'", 'order' => 'customers.customerName', 'rows' => 2 ];

?>

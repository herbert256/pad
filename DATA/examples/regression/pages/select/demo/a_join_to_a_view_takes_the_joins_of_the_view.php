<?php

  // A join to a view that joins a table of its own, and whose where names it, takes that
  // join inside the view's rows: the sales staff of the Paris office, found through the
  // offices table. The view's where went in without its join and the select ended on
  // "Unknown column 'offices.city'". Over the Classic Models tables of the demo database.

  $padSelect ['offices']        = [ 'key' => 'officeCode' ];
  $padSelect ['parisStaff']     = [ 'db' => 'employees', 'key' => 'employeeNumber', 'where' => "offices.city = 'Paris'",
                                    'join' => [ 'inner' => 'offices', 'key' => 'employees.officeCode' ],
                                    'fields' => [ 'lastName' => 'rep' ] ];
  $padSelect ['parisCustomers'] = [ 'db' => 'customers', 'fields' => 'customers.customerName', 'rows' => 3,
                                    'join' => [ 'inner' => 'parisStaff', 'key' => 'customers.salesRepEmployeeNumber' ],
                                    'order' => 'customers.customerName' ];

?>

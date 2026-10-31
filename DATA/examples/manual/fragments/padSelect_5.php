<?php

  $padSelect ['customers'] = [ 'key' => 'customerNumber', 'order' => 'customerName' ];
  $padSelect ['repName']   = [ 'db' => 'employees', 'key' => 'employeeNumber', 'fields' => [ 'lastName' => 'rep' ] ];

  $padSelect ['japan'] = [ 'base'   => 'customers',
                           'fields' => 'customers.customerName',
                           'join'   => [ 'left' => 'repName', 'key' => 'customers.salesRepEmployeeNumber' ],
                           'where'  => "customers.country = 'Japan'",
                           'order'  => 'customers.customerName' ];

?>

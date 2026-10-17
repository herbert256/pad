<?php

  // A virtual table based on another virtual table selects from the table at the end of
  // the chain, with both their wheres: base= was resolved one level deep, and with no db
  // in the middle view the select read a table named after it - "Table 'demo.sales'
  // doesn't exist".

  $padSelect ['employees']  = [ 'db' => 'employees', 'key' => 'employeeNumber', 'order' => 'lastName' ];
  $padSelect ['sales']      = [ 'base' => 'employees', 'where' => "jobTitle like 'Sales%'" ];
  $padSelect ['salesSF']    = [ 'base' => 'sales', 'where' => "officeCode = '1'" ];
  $padSelect ['salesAll']   = [ 'base' => 'sales' ];

?>

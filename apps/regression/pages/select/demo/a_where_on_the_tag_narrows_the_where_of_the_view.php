<?php

  // A where= on the tag narrows the table its declaration makes: the view's where - and the
  // wheres of its base chain - hold beside it. Over the employees of the demo database: the
  // sales staff, and the sales staff of the Paris office.

  $padSelect ['sales']      = [ 'db' => 'employees', 'key' => 'employeeNumber', 'order' => 'lastName',
                                'where' => "jobTitle like 'Sales%'" ];
  $padSelect ['salesParis'] = [ 'base' => 'sales', 'where' => "officeCode = '4'" ];

  $office = '4';

?>

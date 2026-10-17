<?php

  // A virtual table nested in a related table is related to it through its base: a view of
  // the employees inside {offices} lists the employees of that office. Only the outer
  // tag's base= chain was walked, so every office listed every sales rep of the view, and
  // a view without a where of its own ended the page on a missing relation.

  $padSelect ['offices']   = [ 'db' => 'offices',   'key' => 'officeCode' ];
  $padSelect ['employees'] = [ 'db' => 'employees', 'key' => 'employeeNumber' ];

  $padSelect ['salesReps'] = [ 'base' => 'employees', 'where' => "jobTitle like 'Sales%'", 'order' => 'lastName' ];
  $padSelect ['staffOf']   = [ 'base' => 'employees', 'order' => 'lastName' ];

  $padRelations ['employees'] ['offices'] = 'officeCode';

?>

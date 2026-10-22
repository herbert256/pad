<?php

  // A join to a view whose table is named with its schema - demo.offices - or with an alias
  // - offices o - joins only the rows of its where, under the table's own name or the alias,
  // by which the key and the fields name it: those names made it no plain name, and every
  // row was joined - every employee, not those of the offices in the USA. Over the Classic
  // Models tables of the demo database.

  $padSelect ['usSchema']    = [ 'db' => 'demo.offices', 'key' => 'officeCode', 'where' => "country = 'USA'",
                                 'fields' => [ 'city' => 'office' ] ];
  $padSelect ['usAlias']     = [ 'db' => 'offices o', 'key' => 'officeCode', 'where' => "o.country = 'USA'",
                                 'fields' => [ 'city' => 'office' ] ];

  $padSelect ['staffSchema'] = [ 'db' => 'employees', 'fields' => 'employees.lastName', 'rows' => 3,
                                 'order' => 'employees.lastName',
                                 'join' => [ 'inner' => 'usSchema', 'key' => 'employees.officeCode' ] ];
  $padSelect ['staffAlias']  = [ 'db' => 'employees', 'fields' => 'employees.lastName', 'rows' => 3,
                                 'order' => 'employees.lastName',
                                 'join' => [ 'inner' => 'usAlias', 'key' => 'employees.officeCode' ] ];

?>

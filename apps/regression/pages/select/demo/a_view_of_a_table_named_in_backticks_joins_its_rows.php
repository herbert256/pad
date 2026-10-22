<?php

  // A join to a view whose table is named in backticks - the way to name one that is a
  // reserved word - joins only the rows of its where, as a join to any view: the backticks
  // made it no plain name, and every row of the table was joined - every employee, not those
  // of the three offices in the USA. Over the Classic Models tables of the demo database.

  $padSelect ['usOffices'] = [ 'db' => '`offices`', 'key' => 'officeCode', 'where' => "country = 'USA'" ];
  $padSelect ['usStaff']   = [ 'db' => 'employees', 'fields' => 'employees.lastName', 'order' => 'employees.lastName',
                               'join' => [ 'inner' => 'usOffices', 'key' => 'employees.officeCode' ] ];

?>

<?php

  // A relation written [ 'key' => field ] names the field of the first table that holds the
  // declared key of the second - and offices declares none here for it to meet.

  $padSelect ['staffOf'] = [ 'db' => 'employees', 'key' => 'employeeNumber' ];
  $padSelect ['officeOf'] = [ 'db' => 'offices' ];

  $padRelations ['staffOf'] ['officeOf'] = [ 'key' => 'officeCode' ];

?>

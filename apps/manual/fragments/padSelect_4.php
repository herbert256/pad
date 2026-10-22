<?php

  $padSelect ['employees'] = [ 'key' => 'employeeNumber', 'order' => 'lastName' ];
  $padSelect ['managers']  = [ 'base' => 'employees' ];

  $padRelations ['employees'] ['managers'] = [ 'reportsTo' => 'employeeNumber' ];

?>

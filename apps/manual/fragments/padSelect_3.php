<?php

  $padSelect ['offices']   = [ 'key' => 'officeCode',     'order' => 'city'     ];
  $padSelect ['employees'] = [ 'key' => 'employeeNumber', 'order' => 'lastName' ];

  $padRelations ['employees'] ['offices'] = 'officeCode';

?>

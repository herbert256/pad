<?php

  $pSql_host     = '127.0.0.1';
  $pSql_database = 'demo';
  $pSql_user     = 'demo';
  $pSql_password = 'demo';

  $pDb_tables ['table1'] = [ ];
  $pDb_tables ['table2'] = [ ];
  $pDb_tables ['table3'] = [ ];
  $pDb_tables ['table4'] = [ ];

  $pDb_tables ['test1']    = [ 'base' => 'table1',                                             ] ;
  $pDb_tables ['test12']   = [ 'base' => 'table1', 'union' => 'table2'                         ] ;
  $pDb_tables ['test1234'] = [ 'base' => 'table1', 'union' => [ 'table2', 'table3', 'table4' ] ] ;

?>
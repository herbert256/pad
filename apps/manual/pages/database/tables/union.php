<?php

  $pad_sql_host     = '127.0.0.1';
  $pad_sql_database = 'demo';
  $pad_sql_user     = 'demo';
  $pad_sql_password = 'demo';

  $pad_db_tables ['table1'] = [ ];
  $pad_db_tables ['table2'] = [ ];
  $pad_db_tables ['table3'] = [ ];
  $pad_db_tables ['table4'] = [ ];

  $pad_db_tables ['test1']    = [ 'base' => 'table1',                                             ] ;
  $pad_db_tables ['test12']   = [ 'base' => 'table1', 'union' => 'table2'                         ] ;
  $pad_db_tables ['test1234'] = [ 'base' => 'table1', 'union' => [ 'table2', 'table3', 'table4' ] ] ;

?>
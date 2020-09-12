<?php

  $pad_sql_host     = '127.0.0.1';
  $pad_sql_database = 'demo';
  $pad_sql_user     = 'demo';
  $pad_sql_password = 'demo';

  $pad_db_tables ['table1'] = [ 'key' => 'key', 'fields' => ['key' => 'key1', 'text' => 'text1'] ];
  $pad_db_tables ['table2'] = [ 'key' => 'key', 'fields' => ['key' => 'key2', 'text' => 'text2'] ];
  $pad_db_tables ['table3'] = [ 'key' => 'key', 'fields' => ['key' => 'key3', 'text' => 'text3'] ];
  $pad_db_tables ['table4'] = [ 'key' => 'key', 'fields' => ['key' => 'key4', 'text' => 'text4'] ];

  $pad_db_tables ['join']     = [ 'base' => 'table1', 'join' => 'table2' ];

  $pad_db_tables ['inner']    = [ 'base' => 'table1', 'join' => [ 'inner'         => 'table2', 'key'=> 'table1.key'] ];
  $pad_db_tables ['natural1'] = [ 'base' => 'table1', 'join' => [ 'natural'       => 'table2'                      ] ];
  $pad_db_tables ['left']     = [ 'base' => 'table1', 'join' => [ 'left'          => 'table2', 'key'=> 'table1.key'] ];
  $pad_db_tables ['natural2'] = [ 'base' => 'table1', 'join' => [ 'natural left'  => 'table2'                      ] ];
  $pad_db_tables ['right']    = [ 'base' => 'table1', 'join' => [ 'right'         => 'table2', 'key'=> 'table1.key'] ];
  $pad_db_tables ['natural3'] = [ 'base' => 'table1', 'join' => [ 'natural right' => 'table2'                      ] ];
  $pad_db_tables ['cross']    = [ 'base' => 'table1', 'join' => [ 'cross'         => 'table2'                      ] ];

  $pad_db_tables ['multi']    = [ 'base' => 'table1', 'join' => [ ['inner' => 'table2', 'key'=> 'table1.key'],
                                                                  ['inner' => 'table3', 'key'=> 'table1.key'],
                                                                  ['inner' => 'table4', 'key'=> 'table1.key'] ] ];

?>
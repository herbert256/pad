<?php

  $pSql_host     = '127.0.0.1';
  $pSql_database = 'demo';
  $pSql_user     = 'demo';
  $pSql_password = 'demo';

  $pDb_tables ['table1'] = [ 'key' => 'key', 'fields' => ['key' => 'key1', 'text' => 'text1'] ];
  $pDb_tables ['table2'] = [ 'key' => 'key', 'fields' => ['key' => 'key2', 'text' => 'text2'] ];
  $pDb_tables ['table3'] = [ 'key' => 'key', 'fields' => ['key' => 'key3', 'text' => 'text3'] ];
  $pDb_tables ['table4'] = [ 'key' => 'key', 'fields' => ['key' => 'key4', 'text' => 'text4'] ];

  $pDb_tables ['join']     = [ 'base' => 'table1', 'join' => 'table2' ];

  $pDb_tables ['inner']    = [ 'base' => 'table1', 'join' => [ 'inner'         => 'table2', 'key'=> 'table1.key'] ];
  $pDb_tables ['natural1'] = [ 'base' => 'table1', 'join' => [ 'natural'       => 'table2'                      ] ];
  $pDb_tables ['left']     = [ 'base' => 'table1', 'join' => [ 'left'          => 'table2', 'key'=> 'table1.key'] ];
  $pDb_tables ['natural2'] = [ 'base' => 'table1', 'join' => [ 'natural left'  => 'table2'                      ] ];
  $pDb_tables ['right']    = [ 'base' => 'table1', 'join' => [ 'right'         => 'table2', 'key'=> 'table1.key'] ];
  $pDb_tables ['natural3'] = [ 'base' => 'table1', 'join' => [ 'natural right' => 'table2'                      ] ];
  $pDb_tables ['cross']    = [ 'base' => 'table1', 'join' => [ 'cross'         => 'table2'                      ] ];

  $pDb_tables ['multi']    = [ 'base' => 'table1', 'join' => [ ['inner' => 'table2', 'key'=> 'table1.key'],
                                                                  ['inner' => 'table3', 'key'=> 'table1.key'],
                                                                  ['inner' => 'table4', 'key'=> 'table1.key'] ] ];

?>
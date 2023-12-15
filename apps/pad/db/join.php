<?php

 
  $padTables ['table1'] = [ 'key' => 'key', 'fields' => ['key' => 'key1', 'text' => 'text1'] ];
  $padTables ['table2'] = [ 'key' => 'key', 'fields' => ['key' => 'key2', 'text' => 'text2'] ];
  $padTables ['table3'] = [ 'key' => 'key', 'fields' => ['key' => 'key3', 'text' => 'text3'] ];
  $padTables ['table4'] = [ 'key' => 'key', 'fields' => ['key' => 'key4', 'text' => 'text4'] ];

  $padTables ['join']     = [ 'base' => 'table1', 'join' => 'table2' ];

  $padTables ['inner']    = [ 'base' => 'table1', 'join' => [ 'inner'         => 'table2', 'key'=> 'table1.key'] ];
  $padTables ['natural1'] = [ 'base' => 'table1', 'join' => [ 'natural'       => 'table2'                      ] ];
  $padTables ['left']     = [ 'base' => 'table1', 'join' => [ 'left'          => 'table2', 'key'=> 'table1.key'] ];
  $padTables ['natural2'] = [ 'base' => 'table1', 'join' => [ 'natural left'  => 'table2'                      ] ];
  $padTables ['right']    = [ 'base' => 'table1', 'join' => [ 'right'         => 'table2', 'key'=> 'table1.key'] ];
  $padTables ['natural3'] = [ 'base' => 'table1', 'join' => [ 'natural right' => 'table2'                      ] ];
  $padTables ['cross']    = [ 'base' => 'table1', 'join' => [ 'cross'         => 'table2'                      ] ];

  $padTables ['multi']    = [ 'base' => 'table1', 'join' => [ ['inner' => 'table2', 'key'=> 'table1.key'],
                                                              ['inner' => 'table3', 'key'=> 'table1.key'],
                                                              ['inner' => 'table4', 'key'=> 'table1.key'] ] ];
   
?>
<?php

  $padSelect ['table1'] = [ 'key' => 'key', 'fields' => ['key' => 'key1', 'text' => 'text1'] ];
  $padSelect ['table2'] = [ 'key' => 'key', 'fields' => ['key' => 'key2', 'text' => 'text2'] ];
  $padSelect ['table3'] = [ 'key' => 'key', 'fields' => ['key' => 'key3', 'text' => 'text3'] ];
  $padSelect ['table4'] = [ 'key' => 'key', 'fields' => ['key' => 'key4', 'text' => 'text4'] ];

  $padSelect ['join']     = [ 'base' => 'table1', 'join' => 'table2' ];

  $padSelect ['inner']    = [ 'base' => 'table1', 'join' => [ 'inner'         => 'table2', 'key'=> 'table1.key'] ];
  $padSelect ['natural1'] = [ 'base' => 'table1', 'join' => [ 'natural'       => 'table2'                      ] ];
  $padSelect ['left']     = [ 'base' => 'table1', 'join' => [ 'left'          => 'table2', 'key'=> 'table1.key'] ];
  $padSelect ['natural2'] = [ 'base' => 'table1', 'join' => [ 'natural left'  => 'table2'                      ] ];
  $padSelect ['right']    = [ 'base' => 'table1', 'join' => [ 'right'         => 'table2', 'key'=> 'table1.key'] ];
  $padSelect ['natural3'] = [ 'base' => 'table1', 'join' => [ 'natural right' => 'table2'                      ] ];
  $padSelect ['cross']    = [ 'base' => 'table1', 'join' => [ 'cross'         => 'table2'                      ] ];

  $padSelect ['multi']    = [ 'base' => 'table1', 'join' => [ ['inner' => 'table2', 'key'=> 'table1.key'],
                                                              ['inner' => 'table3', 'key'=> 'table1.key'],
                                                              ['inner' => 'table4', 'key'=> 'table1.key'] ] ];
?>
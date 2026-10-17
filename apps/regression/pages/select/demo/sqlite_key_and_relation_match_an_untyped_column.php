<?php

  // On SQLite a key bound on a select tag and the relation to the enclosing select compare
  // a number as a number: the value went in as quoted text, and a column declared without
  // a type has no affinity, so '2' never equalled the integer 2 - {boards $id=2} found no
  // board, and every board's topics came out empty. A key that only looks like a number,
  // '007', stays text.

  $GLOBALS ['padSqlConnect'] = padDbSqlite ( ':memory:' );

  db ( "create table boards (id, name)" );
  db ( "create table topics (id integer primary key, board_id, title text)" );
  db ( "create table codes (code, label)" );
  db ( "insert into boards values (1, 'one'), (2, 'two')" );
  db ( "insert into topics (board_id, title) values (1, 'a'), (1, 'b'), (2, 'c')" );
  db ( "insert into codes values ('007', 'bond'), (7, 'seven')" );

  $padSelect ['boards'] = [ 'db' => 'boards', 'key' => 'id' ];
  $padSelect ['topics'] = [ 'db' => 'topics', 'key' => 'id' ];
  $padSelect ['codes']  = [ 'db' => 'codes',  'key' => 'code' ];

  $padRelations ['topics'] ['boards'] = [ 'board_id' => 'id' ];

  $wanted = '2';
  $code   = '007';

?>

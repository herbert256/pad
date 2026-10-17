<?php

  // On SQLite an insert answers the id it gave its row also when that id is the one the
  // insert before it gave: an order and its first line, both tables holding 6 rows, are
  // rows 7 and 7, and an id handed out again after the last row was deleted is 7 once more.
  // The connection's last insert id did not move, so both answered 1, their row count.

  $GLOBALS ['padSqlConnect'] = padDbSqlite ( ':memory:' );

  db ( "create table orders (id integer primary key, x text)" );
  db ( "create table lines (id integer primary key, x text)" );
  db ( "insert into orders (id, x) values (6, 'old')" );
  db ( "insert into lines (id, x) values (6, 'old')" );

  $order = db ( "insert into orders (x) values ('new order')" );
  $line  = db ( "insert into lines (x) values ('new line')" );

  db ( "delete from lines where id = 7" );

  $again = db ( "insert into lines (x) values ('again')" );
  $none  = db ( "insert or ignore into lines (id, x) values (7, 'twice')" );

?>

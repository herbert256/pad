<?php

  // On SQLite an insert answers the id of the row it inserted, and an insert that gave no
  // row a new id answers its row count, as on MySQL: the connection's last insert id was
  // taken as it stood, so INSERT OR IGNORE of a duplicate, an empty INSERT ... SELECT and
  // an insert into a WITHOUT ROWID table answered 2, the id of the insert before them, and
  // the upsert that updated row 2 of u answered 5, the last id of another table. (An
  // ignored row still uses up an AUTOINCREMENT number, so the next new row is 6.)

  $GLOBALS ['padSqlConnect'] = padDbSqlite ( ':memory:' );

  db ( "create table u (id integer primary key autoincrement, email text unique, n int default 0)" );
  db ( "create table other (id integer primary key autoincrement, x text)" );
  db ( "create table plain (k text primary key, v text) without rowid" );

  $first   = db ( "insert into u (email) values ('a@x')" );
  $second  = db ( "insert into u (email) values ('b@x')" );
  $ignored = db ( "insert or ignore into u (email) values ('a@x')" );
  $nothing = db ( "insert into u (email) values ('a@x') on conflict do nothing" );
  $empty   = db ( "insert into u (email) select email from u where 0" );
  $plain   = db ( "insert into plain (k, v) values ('k', 'v')" );
  $others  = db ( "insert into other (x) values ('1'), ('2'), ('3'), ('4'), ('5')" );
  $upsert  = db ( "insert into u (email) values ('b@x') on conflict (email) do update set n = n + 1" );
  $third   = db ( "insert into u (email) values ('c@x')" );

?>

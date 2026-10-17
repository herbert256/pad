<?php

  // On SQLite numeric text goes into a bare placeholder, or a select's $name, as a number
  // only when SQLite gives the same text back: '007' became the integer 7, so the code
  // '007' found the row of code '7', the zip '01234' was stored as '1234' and '1.10' as
  // '1.1'. A numeral like '3' stays a number, so a count still compares with it.

  $GLOBALS ['padSqlConnect'] = padDbSqlite ( ':memory:' );

  db ( "create table codes (code text primary key, zip varchar(10), label text)" );
  db ( "insert into codes values ('007', '01234', 'bond'), ('7', '1234', 'seven')" );

  $byCode = db ( "field label from codes where code = {0}",                   [ '007' ] );
  $byZip  = db ( "field zip from codes where zip = {0}",                      [ '01234' ] );
  $inList = db ( "array code from codes where code in ({0}) order by code",   [ [ '007', '7' ] ] );
  $inList = implode ( ' ', array_column ( $inList, 'code' ) );

  db ( "insert into codes values ({0}, {1}, 'kept')", [ '0042', '1.10' ] );

  $stored = db ( "record code, zip from codes where label = 'kept'" );
  $count  = db ( "field 'three' where (select count(*) from codes) = {0}",    [ '3' ] );

  $padSelect ['codes'] = [ 'db' => 'codes', 'key' => 'code' ];

  $c = '007';

?>

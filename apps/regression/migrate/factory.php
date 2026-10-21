<?php

  // padFactory inserts rows through db () with placeholders: the rows come back with the
  // id the insert answered, and a value that reads as SQL stays a value.

  padMigrateFresh ( TRUE );
  padFakeSeed ( 42 );

  $made = padFactory ( 'customers', 3, fn ( $i ) => [
    'name'  => $i == 3 ? "Robert'); drop table customers; --" : padFakeName (),
    'city'  => padFakeCity (),
    'email' => $i == 2 ? NULL : padFakeEmail (),
  ] );

  $fixed = padFactory ( 'customers', 2, [ 'name' => 'Same', 'city' => 'Here' ] );

  // A table whose columns are named by words the SQL reserves is filled as any other.

  db ( 'create table "group" (id integer primary key, "order" integer, "key" text)' );

  $reserved = padFactory ( 'group', 2, fn ( $i ) => [ 'order' => $i, 'key' => "k$i" ] );
  $keys     = implode ( ', ', array_column ( db ( 'array "key" from "group" order by "order"' ), 'key' ) );

  db ( 'drop table "group"' );

  $rows  = db ( "array id, name, city, coalesce(email, 'NULL') as email from customers order by id" );
  $ids   = implode ( ', ', array_column ( array_merge ( $made, $fixed ), 'id' ) );

?>

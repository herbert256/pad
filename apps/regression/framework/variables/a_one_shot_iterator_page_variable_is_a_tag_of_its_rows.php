<?php

  // An iterator that cannot rewind - a PDOStatement, a NoRewindIterator - is read once, and
  // its rows take its place: the type check read it, and the tag found it empty.

  $pdo = new PDO ( 'sqlite::memory:' );
  $pdo -> exec ( "create table t ( n int, name text )" );
  $pdo -> exec ( "insert into t values ( 1, 'a' ), ( 2, 'b' ), ( 3, 'c' )" );

  $rows = $pdo -> query ( "select n, name from t order by n" );
  $rows -> setFetchMode ( PDO::FETCH_ASSOC );

  $once = new NoRewindIterator ( new ArrayIterator ( [ [ 'n' => 4 ], [ 'n' => 5 ] ] ) );

?>

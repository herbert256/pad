<?php

  // On SQLite a backslash in a quoted literal is a character like any other, so 'C:\' ends
  // where its quote does and a $name after it is bound. The bind took the backslash for an
  // escape on every driver: the quote that ends the literal counted as escaped, the rest of
  // the statement as quoted, and $n went to SQLite as written - a parameter of its own, NULL.

  $GLOBALS ['padSqlConnect'] = padDbSqlite ( ':memory:' );

  db ( "create table staff (name text, folder text)" );
  db ( "insert into staff values ('bob', 'C:\\'), ('jim', 'D:\\')" );

  $n = 'bob';

?>

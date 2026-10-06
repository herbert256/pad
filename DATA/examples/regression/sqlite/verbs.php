<?php

  // db()'s reading verbs on SQLite, answering in the shapes they have on MySQL.

  $field  = db ( "field phone from staff where name = 'jim'" );
  $joe    = db ( "record name, salary from staff where name = 'joe'" );
  $staff  = db ( "array name, phone from staff order by name" );
  $found  = db ( "check staff where name = 'bob'" );
  $none   = db ( "check staff where name = 'nobody'" );
  $count  = db ( "field count(*) from staff" );

  $types  = gettype ( $count ) . ' ' . gettype ( $joe ['salary'] );

?>

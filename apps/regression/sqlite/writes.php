<?php

  // The writing verbs on SQLite: insert answers the new id, update and delete the rows they
  // touched. The table is emptied first, so every run starts alike.

  db ( "delete from notes" );
  db ( "delete from sqlite_sequence where name = 'notes'" );

  $first   = db ( "insert into notes (text) values ('{0}')", [ "it's one" ] );
  $second  = db ( "insert into notes (text) values ({0})",   [ 'two' ] );
  $updated = db ( "update notes set text = upper(text)" );
  $deleted = db ( "delete from notes where id = {0}",        [ $first ] );
  $notes   = db ( "array id, text from notes" );

?>

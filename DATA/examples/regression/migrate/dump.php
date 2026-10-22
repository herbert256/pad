<?php

  // A migration written as sqlite3's .dump writes it - BEGIN TRANSACTION first, COMMIT last
  // - runs as one step: the step is a transaction of its own, and the file's own
  // transaction lines are passed over. The step is run here the way padMigrate runs a .sql
  // file, recorded under a name of its own and taken out again after.

  $dumpSql = "PRAGMA foreign_keys=OFF;\nBEGIN TRANSACTION;\nCREATE TABLE dumped (id integer primary key, note text);\n"
           . "INSERT INTO dumped VALUES(1,'hello');\nINSERT INTO dumped VALUES(2,'world');\nCOMMIT;\n";

  $padMigrateQuiet = TRUE;

  db ( 'drop table if exists dumped' );
  padMigrateTable ( padDbApp () );

  $dumpRan = padMigrateStep ( padDbApp (), [ 'file' => '2026_10_09_090000_dump.sql' ], $dumpSql,
                              [ "insert into pad_migrations (migration, batch, ran_at) values ({0}, {1}, {2})",
                                [ '2026_10_09_090000_dump', 99, '2026-10-09 09:00:00' ] ] );

  $dumpError = $dumpRan ? '' : ( $padMigrateError ?? '' );
  $dumpNotes = $dumpRan ? implode ( ', ', array_column ( db ( 'array note from dumped order by id' ), 'note' ) ) : '';

  db ( 'drop table if exists dumped' );
  db ( "delete from pad_migrations where migration = '2026_10_09_090000_dump'" );

  $padMigrateQuiet = FALSE;

?>

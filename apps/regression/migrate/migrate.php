<?php

  // padMigrate runs the pending migrations - a .sql file with its trigger, a .php one whose
  // up is a function - as one batch, and a second run finds nothing to do. Every run starts
  // from nothing: padMigrateFresh drops the tables and migrates again.

  $fresh  = implode ( ', ', padMigrateFresh ( TRUE ) );
  $again  = count ( padMigrate () );
  $tables = migrateTables ();
  $status = migrateStatus ();

?>

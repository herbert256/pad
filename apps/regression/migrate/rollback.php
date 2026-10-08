<?php

  // padMigrateRollback runs the down of the last batch, newest first: a .sql migration
  // through its .down.sql, a .php one through its 'down'. The trigger is moved to a batch of
  // its own first, so one step takes back only that one, and a second step the rest.

  padMigrateFresh ( TRUE );

  db ( "update pad_migrations set batch = 2 where migration like '%order_trigger'" );

  $one     = implode ( ', ', padMigrateRollback () );
  $tables1 = migrateTables ();
  $status1 = migrateStatus ();
  $two     = implode ( ', ', padMigrateRollback ( 5 ) );
  $tables2 = migrateTables ();
  $none    = count ( padMigrateRollback () );
  $back    = implode ( ', ', padMigrate () );
  $status2 = migrateStatus ();

?>

<?php

  // padMigrate ( TRUE ) runs nothing and answers the statements each pending migration
  // would send: a .sql file split into its statements, a .php one's function called with
  // its db () statements collected instead of sent.

  padMigrateFresh ( TRUE );
  padMigrateRollback ();

  $pretend = [];

  foreach ( padMigrate ( TRUE ) as $name => $statements )
    $pretend [] = [ 'name' => $name, 'count' => count ( $statements ),
                    'first' => preg_replace ( '/\s+/', ' ', $statements [0] ) ];

  $tables = migrateTables ();
  $status = migrateStatus ();

?>

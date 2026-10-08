<?php

  // The pages of this application share one database and each starts by dropping it, so
  // two of them at once - pad lint renders four at a time - would pull the tables from
  // under each other: a request holds this lock from here until it ends.

  if ( ! is_dir ( DATA . 'migrate' ) )
    @mkdir ( DATA . 'migrate', 0755, TRUE );

  $GLOBALS ['migrateAlone'] = fopen ( DATA . 'migrate/regression.lock', 'c' );

  flock ( $GLOBALS ['migrateAlone'], LOCK_EX );

  // The tables of the application database, by name, comma separated - what a page shows
  // to prove a migration made them, or its down took them away.

  function migrateTables () {

    $names = db ( "array name from sqlite_master where type in ('table', 'trigger') and name not like 'sqlite_%' order by type, name" );

    return implode ( ', ', array_column ( $names, 'name' ) );

  }

  // padMigrateStatus as text lines: yes/no, the batch, the name.

  function migrateStatus () {

    $lines = [];

    foreach ( padMigrateStatus () as $row )
      $lines [] = ( $row ['ran'] ? 'yes ' : 'no  ' ) . str_pad ( $row ['batch'], 2 ) . $row ['migration'];

    return implode ( "\n", $lines );

  }

?>

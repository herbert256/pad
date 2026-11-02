<?php

  define ( 'PAD', dirname ( __DIR__, 2 ) . '/pad/' );
  require PAD . 'lib/subsystem.php';

  $directories = array_unique ( array_merge (
    array_column ( padSubsystems, 0 ), array_values ( padSubsystemKinds )
  ) );

  foreach ( $directories as $directory ) {
    if ( str_contains ( $directory, '/' ) or str_contains ( $directory, '\\' ) )
      throw new RuntimeException ( "Nested subsystem registry: $directory" );
    if ( ! is_dir ( PAD . "subs/$directory/types" ) )
      throw new RuntimeException ( "Missing subsystem registry: $directory" );
  }

  $found = [];
  $tree = new RecursiveIteratorIterator (
    new RecursiveDirectoryIterator ( PAD . 'subs', FilesystemIterator::SKIP_DOTS ),
    RecursiveIteratorIterator::SELF_FIRST
  );
  foreach ( $tree as $entry ) {
    if ( ! $entry->isDir () or $entry->getFilename () !== 'types' ) continue;
    $directory = substr ( $entry->getPath (), strlen ( PAD . 'subs/' ) );
    if ( str_contains ( $directory, '/' ) )
      throw new RuntimeException ( "Subsystems must be one level below pad/subs/: $directory" );
    if ( ! in_array ( $directory, $directories, TRUE ) )
      throw new RuntimeException ( "Unregistered subsystem: $directory" );
    $found [] = $directory;
  }

  sort ( $directories );
  sort ( $found );
  if ( $directories !== $found )
    throw new RuntimeException ( 'Subsystem registries do not match the directories' );

  echo count ( $found ) . " subsystem registries live directly under pad/subs/\n";

?>

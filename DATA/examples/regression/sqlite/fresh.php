<?php

  // A database file that does not exist yet is built from the setup file on its first
  // connection - the application's own was built that way once, so this page builds a
  // second one beside it, reads it, and removes it again.

  $freshFile = 'sqlite/fresh.sqlite';

  @unlink ( DATA . $freshFile );

  $freshConnect = padDbSqlite ( $freshFile, '_install/demo.sql' );
  $freshBuilt   = file_exists ( DATA . $freshFile ) ? 'built' : 'missing';
  $freshCount   = padDbPart2 ( $freshConnect, "field count(*) from staff", [] );

  $freshConnect = NULL;

  @unlink ( DATA . $freshFile );
  @unlink ( DATA . "$freshFile.lock" );

?>

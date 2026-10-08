<?php

  // The health fixture: _common off, the health check on, an SQLite database in memory -
  // or, with ?nodb, a MySQL server that is not there, so the database check fails.

  $padCommon      = FALSE;
  $padHealth      = TRUE;

  $padSqlDriver   = padEnv ( 'padSqlDriver' );
  $padSqlDatabase = padEnv ( 'padSqlDatabase' );

  if ( isset ( $_GET ['nodb'] ) ) {
    $padSqlDriver   = 'mysql';
    $padSqlHost     = '127.0.0.1:1';
    $padSqlUser     = 'nobody';
    $padSqlPassword = 'secret-password';
    $padSqlDatabase = 'nothing';
  }

?>

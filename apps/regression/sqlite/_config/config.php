<?php

  // The sqlite application: its .env makes the application database an SQLite file under
  // DATA/, built on the first request from _install/demo.sql - no database server at all.

  $padCommon      = FALSE;

  $padSqlDriver   = padEnv ( 'padSqlDriver' );
  $padSqlDatabase = padEnv ( 'padSqlDatabase' );
  $padSqlSetup    = padEnv ( 'padSqlSetup' );

?>

<?php

  // The sqlite application: the application database is an SQLite file under DATA/, built
  // on the first request from _install/demo.sql - no database server at all.

  $padCommon      = FALSE;

  $padSqlDriver   = 'sqlite';
  $padSqlDatabase = 'sqlite/regression.sqlite';
  $padSqlSetup    = '_install/demo.sql';

?>

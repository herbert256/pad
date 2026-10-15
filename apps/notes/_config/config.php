<?php

  // Notes keeps its notes in SQLite - one file under DATA/, built from _install/schema.sql
  // on the first request that needs it - on a server as in the browser, where pad pack runs
  // the application offline and keeps the file in IndexedDB.

  $padCommon      = FALSE;
  $padCsrf        = TRUE;

  $padSqlDriver   = 'sqlite';
  $padSqlDatabase = 'notes/notes.sqlite';
  $padSqlSetup    = '_install/schema.sql';

?>

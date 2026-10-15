<?php

  // Kanban keeps its board in SQLite - DATA/kanban/kanban.sqlite, built from
  // _install/schema.sql on the first request - on a server as in the browser, where
  // pad pack kanban runs it offline and every window of it shares the one database.

  $padCommon      = FALSE;
  $padCsrf        = TRUE;

  $padSqlDriver   = 'sqlite';
  $padSqlDatabase = 'kanban/kanban.sqlite';
  $padSqlSetup    = '_install/schema.sql';

?>

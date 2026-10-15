<?php

  // Ledger keeps its books in SQLite - DATA/ledger/ledger.sqlite - built by its migrations,
  // which _inits.php runs when one is pending: a new version of the application brings the
  // database it finds up to date, on a server as in the browser (pad pack ledger).

  $padCommon      = FALSE;
  $padCsrf        = TRUE;

  $padSqlDriver   = 'sqlite';
  $padSqlDatabase = 'ledger/ledger.sqlite';

?>

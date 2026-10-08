<?php

  // The migrate application: its .env makes the application database an SQLite file under
  // DATA/, which its _migrations/ build and its _seeds/ fill - no database server at all.

  $padCommon      = FALSE;

  $padSqlDriver   = padEnv ( 'padSqlDriver' );
  $padSqlDatabase = padEnv ( 'padSqlDatabase' );

?>

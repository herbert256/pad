<?php

  // The events application: its own _events/ hooks, the demo database for the sql event,
  // and the 'ignore' error action, so a page goes on after an error and can show what the
  // error hook heard.

  $padCommon      = FALSE;
  $padErrorAction = 'ignore';

  $padSqlHost     = padEnv ( 'padSqlHost' );
  $padSqlDatabase = padEnv ( 'padSqlDatabase' );
  $padSqlUser     = padEnv ( 'padSqlUser' );
  $padSqlPassword = padEnv ( 'padSqlPassword' );

?>

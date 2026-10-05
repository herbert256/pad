<?php

  // The point of this application: a configuration file reads its secret with padEnv, from
  // the .env file beside it. The engine loads its library before it reads any
  // configuration, so the function is there when this file runs.

  $padCommon = FALSE;

  $padSqlPassword = padEnv ( 'ENVTEST_DB_PASSWORD', 'not read' );

?>

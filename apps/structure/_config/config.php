<?php

  $padErrorAction    = 'pad';

  $padErrorLevel     = 'all';

  $padErrorTry       = TRUE;

  $padErrorLog       = TRUE;
  $padErrorReport    = TRUE;

  $padInfo = '';

  $padOutputType = 'web';

  $padCache = FALSE;

  $padSqlPadHost           = padEnv ( 'padSqlPadHost' );
  $padSqlPadDatabase       = padEnv ( 'padSqlPadDatabase' );
  $padSqlPadUser           = padEnv ( 'padSqlPadUser' );
  $padSqlPadPassword       = padEnv ( 'padSqlPadPassword' );

  $padSqlHost               = padEnv ( 'padSqlHost' );
  $padSqlDatabase           = padEnv ( 'padSqlDatabase' );
  $padSqlUser               = padEnv ( 'padSqlUser' );
  $padSqlPassword           = padEnv ( 'padSqlPassword' );

  $padDirMode  = 0755;
  $padFileMode = 0644;

  $padFmtDate = 'Y-m-d';

  $padSessionVars = [];

  $padDataDefaultStart = [];
  $padDataDefaultEnd   = ['sanitize'];

  $padTidy   = TRUE;
  $padMyTidy = FALSE;

  $padSelect    = [];
  $padRelations = [];

  $padGzip      = FALSE;
  $padCookies   = TRUE;
  $padNoNo      = FALSE;

?>

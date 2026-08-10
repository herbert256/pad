<?php

  // Framework-wide configuration defaults - one place listing every $pad* setting an
  // application may override.
  //
  // Loaded first by start/pad.php and again by inits/config.php, always before _common's
  // and the application's own _config/config.php, so every value here is only a starting
  // point. Covers error action/level/logging, the $padInfo debug mode, $padOutputType,
  // caching, the two database connections (the engine's own 'pad' database and the
  // application database), file and directory modes, date formats, session variables, the
  // default data pipelines, tidy, the select subsystem, and misc request options.

  $padErrorAction    = 'pad';

  $padErrorLevel     = 'all';

  $padErrorTry       = TRUE;

  $padErrorLog       = TRUE;
  $padErrorReport    = TRUE;


  $padEvalTrace = FALSE;

  // The strict syntax check, on by default. The walk reports orphan braces and tags,
  // pairs that never close, options nothing reads, misses behind a type prefix, sections
  // out of order - and the expression side reports a malformed expression or a $field,
  // function, action or script that does not exist. Off, everything falls back to the
  // lenient contract: what nothing claims stays as literal text, and what cannot be
  // evaluated yields empty.

  $padCheckSyntax = TRUE;

  $padInfo = '';

  $padCommon = TRUE;

  $padOutputType = 'web';

  $padCache = FALSE;

  $padSqlPadHost           = '127.0.0.1';
  $padSqlPadDatabase       = 'pad';
  $padSqlPadUser           = 'pad';
  $padSqlPadPassword       = 'pad';

  $padSqlHost               = '127.0.0.1';
  $padSqlDatabase           = 'app';
  $padSqlUser               = 'app';
  $padSqlPassword           = 'app';

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
  $padFastLink  = 32;

?>
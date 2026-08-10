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

  $padErrorTry       = FALSE;

  $padErrorLog       = TRUE;
  $padErrorReport    = TRUE;


  // Strict expression evaluation, off by default. When on, a $field that does not exist is
  // reported inside an expression rather than resolving to empty - the same discipline {$x}
  // already applies, taken into {if}, {echo} and the rest. Left off, an expression keeps
  // PAD's lenient contract that a missing field is empty. (A comparison operator left
  // without an operand is reported regardless of this flag, but only where no pipe value
  // could have stood in for the missing side.)

  $padEvalStrict = FALSE;
  $padEvalTrace = FALSE;

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
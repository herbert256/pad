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

  // The full error report - message, stack, levels, configuration - shown in the page, and
  // the JSON channel for local tooling, are for a request this machine made to itself: the
  // loopback address with no forwarding header. Everyone else gets the request id. FALSE
  // shows the id to every request; set it on a server that sits behind a proxy on the same
  // machine which does not send X-Forwarded-For. Credentials are redacted either way.

  $padDiagnostics    = TRUE;

  // How many error reports $padErrorReport keeps on disk per application, under
  // DATA/dumps/<app>/: the newest ones, older ones are deleted as new ones arrive.

  $padErrorKeep      = 100;


  $padEvalTrace = FALSE;

  // The strict syntax check, on by default. The walk reports orphan braces and tags,
  // pairs that never close, options nothing reads, misses behind a type prefix, sections
  // out of order - and the expression side reports a malformed expression or a $field,
  // function, action or script that does not exist. Off, everything falls back to the
  // lenient contract: what nothing claims stays as literal text, and what cannot be
  // evaluated yields empty.

  $padCheckSyntax = TRUE;

  // Values are text, never template code. Every value that comes out of the data - a {$x}
  // field in any of its sigil forms, what a tag answers, the evaluated side of a ternary,
  // a page fetched from another application - has PAD's syntax characters swapped for
  // inert stand-ins on the way into the page, and they are put back only when the finished
  // page is written out. A field holding {php:getcwd} prints that text instead of running
  // it. Running a value as PAD stays possible, as an explicit choice where it is written:
  // {echo $snippet | code}. Off, the engine re-reads every value as template source, the
  // way it always did.

  $padProtectValues = TRUE;

  // The PHP functions a template may call: {php:strlen 'abc'}, {echo $x | php:strrev}, and
  // a bare name that resolves to nothing else, {$title | ucfirst}. TRUE allows them all; a
  // list - [ 'ucfirst', 'number_format' ] - allows those names and no other; FALSE or []
  // allows none. A template author can write PHP in the page's .php file anyway, so the
  // list matters where a template runs text it did not write: an application that turns
  // $padProtectValues off, or pipes data into {code}.

  $padPhpFunctions = TRUE;

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

  // Which request values become variables of their own - a form field arriving in the
  // template as {$name}. TRUE promotes every POST and GET value; a list - [ 'name',
  // 'email' ] - promotes those names and no other; FALSE or [] none, and the page's PHP
  // reads $_POST and $_GET itself. A cookie becomes a variable only when a list names it.
  // Whatever the setting, a name declared in $padSessionVars is never filled from the
  // request - the session holds it - and an engine name (pad*, pq*, _*) never is.

  $padRequestVars = TRUE;

  $padDataDefaultStart = [];
  $padDataDefaultEnd   = ['sanitize'];

  $padTidy   = TRUE;
  $padMyTidy = FALSE;

  $padSelect    = [];
  $padRelations = [];

  // The host names this server answers to, e.g. [ 'example.com', 'www.example.com' ]: a
  // request naming another host is treated as naming the first. Empty takes the request's
  // own Host header, once it is a well-formed host. $padHostBase, e.g.
  // 'https://example.com/pad/', replaces the whole derived base - behind a proxy.

  $padHosts     = [];
  $padHostBase  = '';

  $padGzip      = FALSE;
  $padCookies   = TRUE;
  $padNoNo      = FALSE;

?>
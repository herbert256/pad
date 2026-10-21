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

  // What happens when an error is raised (inits/error.php loads error/types/<action>.php):
  // 'pad' ends the request with the error page - the full report for this machine, the
  // request id and the application's _errors/ page for a visitor; 'boot' ends it with a
  // plain 500 (the JSON report for a local tool); 'stop' with a plain 500 that says
  // nothing; 'exit' drops it where it stands and ships nothing; 'php' hands the error to
  // PHP's own handling; 'ignore' swallows it and goes on; 'log' writes it to the log and
  // goes on; 'dump' writes the full report under DATA/dumps/ and goes on. The command line
  // and a local tool get 'boot' unless the application chose its own action.

  $padErrorAction    = 'pad';

  // Which PHP errors reach that action (error/error.php padErrorReporting): 'none', 'error',
  // 'warning', 'notice' - each taking in the ones listed before it - or 'all', deprecations
  // as well.

  $padErrorLevel     = 'all';

  // TRUE runs the engine's risky include points - a tag's handler, a field, an expression,
  // the application's PHP files - inside try/catch (try/try.php), so a throw there becomes
  // a PAD error with its template position; FALSE makes those calls bare and leaves an
  // uncaught throwable to PHP.

  $padErrorTry       = TRUE;

  // $padErrorLog writes every error as one [PAD] line - request id, message with its
  // credentials redacted, template file:line:column - to the server's error log
  // (lib/session.php padLogError). $padErrorReport writes the full report to
  // DATA/dumps/<app>/ as well (lib/dump.php), keeping the newest $padErrorKeep of them.

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

  // Designer preview: ?orders&padSample renders orders.pad with the variables of
  // _samples/orders.json instead of running the PHP - no database, no login (lib/sample.php).
  // 'local' honours it for a request this machine makes to itself, TRUE for every request -
  // a design server with no real data behind it - and FALSE never. Skipping the PHP skips
  // the access checks it makes, so it is never TRUE on a server with real data.

  $padSample         = 'local';

  // TRUE has the expression evaluator keep the tokens and the tree of every evaluation in
  // the global $_eval (lib/eval/eval.php padEvalTrace) - for debugging the evaluator itself,
  // costly on a real page.

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

  // The output check in development: TRUE reads every local HTML response back once it has
  // rendered and names duplicate ids, images without alt, form fields without a label and
  // ?page links to pages that do not exist - in a panel in the page and a PAD-Output-Check
  // header. A remote visitor is never checked. One request can ask for it with
  // ?page&padCheckOutput. See lib/outputCheck.php.

  $padCheckOutput = FALSE;

  // Template coverage: TRUE records, for every local request, which templates it read, which
  // of their tags ran and which branch each {if} and {case} took, as one JSON line in
  // DATA/coverage/default.jsonl; a name instead of TRUE records into DATA/coverage/<name>.jsonl.
  // develop/?coverage shows the report and starts and stops a recording of every application
  // around a suite run. See lib/coverage.php.

  $padCoverage = FALSE;

  // Replay real traffic as tests: TRUE records every GET this application answers with a 200
  // - one that brought no cookie but PAD's own ids - with its answer, in DATA/replay/default/;
  // a name instead of TRUE picks the store. develop/?replay replays a store against the code
  // as it stands and names every page whose answer changed. A replay never writes: db() and
  // the application's padFilePut refuse while it runs. See lib/replay.php.

  $padRecord = FALSE;

  // The debug information a request records - '' none, or a comma-separated list of the
  // presets in config/info/: 'trace' (the execution trace in DATA/trace/), 'stats', 'track',
  // 'xml', 'xref', 'info' (stats, track and xref together), 'all', 'none'. Each word
  // includes config/info/<word>.php, which switches the $padInfo* flags explained in
  // config/info/all.php. Set in the configuration; a request cannot switch it on.

  $padInfo = '';

  // The shared application _common takes part (build/base.php, build/libs.php,
  // lib/paths.php): its _lib/ is loaded, its _tags/, _include/, _functions/, _data/,
  // _content/ and _lang/ are looked in after the application's own, its .env is read, and
  // a _common/_inits.pad would frame every page. FALSE keeps the application on its own;
  // _common is then reached only by the common: prefix.

  $padCommon = TRUE;

  // How the result leaves the engine (config/output/<type>.php, exits/output/<type>.php):
  // 'web' the page to the browser; 'file' written to disk, after which the request restarts
  // at $padFileNextPage; 'download' sent as an attachment; 'console' plain text for the
  // command line - the cli SAPI gets it by itself; 'json' and 'csv' the page's $padExpose
  // variables as data instead of the template.

  $padOutputType = 'web';

  // The page variables a page answers as data - for ?page&padFormat=json or csv, for an
  // Accept header that prefers application/json or text/csv, and under the json and csv
  // output types: the response is these variables instead of the rendered template (see
  // lib/expose.php). Set in the page's .php, or in an _inits.php for a directory. Empty,
  // the page answers HTML only; only what is named here ever leaves the server, and an
  // engine name never does.

  $padExpose = [];

  // The page cache (cache/inits.php, cache/exits.php): TRUE keeps the whole answer of a
  // plain GET for an anonymous visitor and answers the next one from it - 304 or the stored
  // body - before any PHP of the application runs. The ages and the backend are set in
  // config/cache.php ($padCacheServerAge must be above 0 for anything to be kept); a page
  // under a _guard.php, one with a {nocache} part that pushes, or one that exposes data
  // is never kept.

  $padCache = FALSE;

  // The fragment cache - {cache 'name'} ... {/cache} and the cache= option on any tag, see
  // lib/fragment.php: 'file' keeps the sections under DATA/cache/fragments/, 'apcu' in shared
  // memory, FALSE renders them every time.

  $padFragmentCache = 'file';

  // Remote data with a cache - {curl 'url', ttl=600}, data='url' with a ttl and the <ttl>
  // of a _data/*.curl file, see lib/curlCache.php: 'file' keeps the copies under
  // DATA/cache/curl/, 'apcu', 'redis' or 'memcached' in that store, FALSE fetches every
  // time. $padCurlStale is how long, in seconds beyond its ttl, a copy is kept to serve
  // when the source fails.

  $padCurlCache = 'file';
  $padCurlStale = 86400;

  // The debug toolbar - a collapsible bar at the foot of the page with the time, the tag
  // tree, the SQL, the caches, the template files, the variables and the session; see
  // exits/_lib/toolbar.php. 'local' (or TRUE) adds it to the web pages of a local request only -
  // never to one from elsewhere, whatever the setting; FALSE never.

  $padToolbar = FALSE;

  // Live reload - a local page reloads itself when a file behind it changes; see
  // exits/_lib/reload.php. 'local' (or TRUE) watches the application, its www/ directory and
  // _common; 'engine' pad/ as well. Local requests only, whatever the setting; FALSE never.

  $padReload = FALSE;

  // {assert $total eq 42} checks its condition only when this is TRUE, and a false one is
  // an error - the test fails. pad test turns it on for its run; FALSE, the default, leaves
  // every {assert} silent: nothing evaluated, nothing shown.

  $padAssert = FALSE;

  // The two database connections - the engine's own 'pad' database and the application
  // database - are no code: their values stand in the .env of the PAD home, and an
  // application's own .env (or _common's) overrides them, see lib/env.php. This file is
  // read once before the engine's library is loaded, so it loads padEnv itself.

  include_once PAD . 'lib/env.php';

  // The engine's own 'pad' database (padDb) - host, database, user, password.

  $padSqlPadHost           = padEnv ( 'padSqlPadHost' );
  $padSqlPadDatabase       = padEnv ( 'padSqlPadDatabase' );
  $padSqlPadUser           = padEnv ( 'padSqlPadUser' );
  $padSqlPadPassword       = padEnv ( 'padSqlPadPassword' );

  // The application database (db() and the database tags) - host, database, user, password.

  $padSqlHost               = padEnv ( 'padSqlHost' );
  $padSqlDatabase           = padEnv ( 'padSqlDatabase' );
  $padSqlUser               = padEnv ( 'padSqlUser' );
  $padSqlPassword           = padEnv ( 'padSqlPassword' );

  // The application database's driver, see lib/db.php: 'mysql' on the four settings above,
  // or 'sqlite', where $padSqlDatabase is the database file - a relative name lives under
  // DATA/ - and $padSqlSetup a .sql file that builds it the first time, when the file does
  // not exist yet. db(), the database tags and the select subsystem work alike on both.

  $padSqlDriver             = padEnv ( 'padSqlDriver' );
  $padSqlSetup              = padEnv ( 'padSqlSetup' );

  // The permissions a directory and a file get when the engine creates them under DATA/ -
  // padFilePut, uploads, logs, dumps, the file caches, migrations and the SQLite database.

  $padDirMode  = 0755;
  $padFileMode = 0644;

  // The format, in PHP's date() letters, of the date pipe when it is given none: {$created |
  // date} writes 2026-10-21 (functions/date.php).

  $padFmtDate = 'Y-m-d';

  // The locale - {trans}, and the trans, currency and localDate functions; see lib/locale.php.
  // $padLocales lists the locales the application speaks: when it holds any, the visitor's
  // ?lang= choice (kept in the padLang cookie), or else Accept-Language, picks among them.
  // $padTimezone, when set, is the timezone the request runs in.

  $padLocale   = 'en';
  $padLocales  = [];
  $padTimezone = '';

  // The globals a session keeps (lib/session.php, inits/parms.php): names listed here, e.g.
  // [ 'session_user', 'cart' ], start the session, are filled from it before the page's PHP
  // runs - empty when the session has none - and written back to it at the end of the
  // request. A request value never fills one of them.

  $padSessionVars = [];

  // Which request values become variables of their own - a form field arriving in the
  // template as {$name}. TRUE promotes every POST and GET value; a list - [ 'name',
  // 'email' ] - promotes those names and no other; FALSE or [] none, and the page's PHP
  // reads $_POST and $_GET itself. A cookie becomes a variable only when a list names it.
  // Whatever the setting, a name declared in $padSessionVars is never filled from the
  // request - the session holds it - and an engine name (pad*, pq*, _*) never is.

  $padRequestVars = TRUE;

  // CSRF protection, see lib/csrf.php. TRUE: every POST (and PUT, PATCH, DELETE) must carry
  // the token of the visitor's session - in the padCsrfToken field, which is added to each
  // <form method="post"> of the page that posts back to this site, and which {csrf} writes,
  // or in an X-CSRF-Token header - or it is answered 403 before the application runs.

  $padCsrf = FALSE;

  // The application key that padEncrypt, padDecrypt and padSignedUrl seal and sign with,
  // see lib/crypt.php: 32 bytes, or 'base64:' followed by 32 bytes in base64. Empty, the
  // key is the file DATA/keys/<application>.key, made on first use; set it - the same on
  // every server - when several servers must read each other's values, and keep it out of
  // version control. A new key makes every value sealed and every link signed before it
  // unreadable.

  $padAppKey = '';

  // Security headers on every web response, see lib/security.php: name => value, a value
  // of '' or FALSE leaves that one out, [] sends none. A header the page's own PHP sent
  // stands. $padCsp is the Content-Security-Policy, '' none; 'nonce', quotes included, in
  // a directive is this request's nonce, which {nonce} writes into <script nonce="...">:
  // "default-src 'self'; script-src 'self' 'nonce'; frame-ancestors 'self'".

  $padSecurityHeaders = [
    'X-Content-Type-Options' => 'nosniff',
    'Referrer-Policy'        => 'strict-origin-when-cross-origin',
  ];

  $padCsp = "frame-ancestors 'self'";

  // The pipes every {$field} - and {#parameter} - runs through (level/var.php):
  // $padDataDefaultStart before the field's own pipes, $padDataDefaultEnd after them.
  // 'sanitize' encodes the characters HTML gives a meaning (functions/sanitize.php); {!x}
  // skips both chains, {echo} never runs them. Emptying the end chain writes every field
  // raw - every value then needs its own escaping.

  $padDataDefaultStart = [];
  $padDataDefaultEnd   = ['sanitize'];

  // The clean-up of a finished HTML page (exits/tidy.php): $padTidy runs PHP's tidy
  // extension with the settings of config/tidy.php - indented, html5 - and a page can ask
  // for it alone with an @tidy@ marker; $padMyTidy, when $padTidy is off, runs PAD's own
  // light text pass (exits/myTidy.php). Neither runs on a fragment, an include request or a
  // data answer.

  $padTidy   = TRUE;
  $padMyTidy = FALSE;

  // The select subsystem (select/_lib/select.php, docs/DATABASE.md): $padSelect declares
  // the tables a template may use as tags - [ 'users' => [ 'key' => 'id' ] ], and virtual
  // tables with base=, where=, order= - and $padRelations their relations - $padRelations
  // ['orders'] ['customers'] = 'customerNumber' - so a nested {select:} follows them. Set in
  // an application's _lib/ or _inits.php.

  $padSelect    = [];
  $padRelations = [];

  // The host names this server answers to, e.g. [ 'example.com', 'www.example.com' ]: a
  // request naming another host is treated as naming the first. Empty takes the request's
  // own Host header, once it is a well-formed host. The engine's fetches of its own pages
  // connect to the server's own address whatever the host (lib/paths.php padSelfConnect),
  // and off php -S to the scheme's port (80, 443) unless the Host names a port this list
  // holds with its name - 'localhost:8080' for a server on a port of its own.
  // $padHostBase, e.g. 'https://example.com/pad/', replaces the whole derived base - behind
  // a proxy - and those fetches then go where it says.

  $padHosts     = [];
  $padHostBase  = '';

  // Clean URLs (lib/route.php): TRUE makes $padGo and $padGoExt write /shop/products/42
  // instead of /shop/?products/42, for a server that hands such paths to the entry point -
  // php -S does by itself, Apache with FallbackResource. A path is resolved either way; this
  // only says which form the links take.

  $padCleanUrls = FALSE;

  // The sitemap (tags/_lib/sitemap.php): TRUE answers ?sitemap.xml - every page of the application,
  // from the file tree - and ?robots.txt pointing to it, when the application has no page of
  // that name. $padSitemapSkip lists pages, or directories with all below them, it leaves
  // out, e.g. [ 'admin', 'login' ]. The {sitemap} tag lists the pages whatever this says.

  $padSitemap     = FALSE;
  $padSitemapSkip = [];

  // Vite builds ({vite} - lib/vite.php): the scripts and stylesheets of an entry come from the
  // manifest Vite writes into www/<app>/<$padViteBuild>/.vite/manifest.json. While the dev
  // server runs - $padViteDev names it, or the file hot in that directory holds its address -
  // they come from the dev server instead, with hot module replacement.

  $padViteBuild = 'build';
  $padViteDev   = '';

  // Template emails ({mail}, padMail - lib/mail.php): 'file' writes every message as an .eml
  // under DATA/mail/<app>/ instead of sending it - development needs no mail server; 'mail'
  // sends through PHP's mail(); any other value names a function of the application that
  // gets the message, for SMTP through a library. $padMailFrom is the sender - empty,
  // noreply@ the host. $padMailKeep is how many messages the file transport keeps per
  // application, the newest.

  $padMailTransport = 'file';
  $padMailFrom      = '';
  $padMailKeep      = 100;

  // Custom error pages (error/_lib/errorPage.php): TRUE renders _errors/<status>.pad of the
  // application - 404, 403, 500, 503 - when it has one, inside its own _errors/_inits.pad
  // when there is one; FALSE keeps the plain text answers.

  $padErrorPages = TRUE;

  // The health check (inits/_lib/health.php): TRUE answers ?up - 200 with a small JSON body when
  // the checks pass, 503 when one fails - for a load balancer or an uptime monitor, when
  // the application has no page of that name.

  $padHealth = FALSE;

  // Cross-origin requests (inits/_lib/cors.php): [] sends no CORS header. Set, e.g.
  // [ 'origins' => [ 'https://app.example.com' ], 'methods' => [ 'GET', 'POST' ],
  //   'headers' => [ 'Content-Type' ], 'credentials' => FALSE, 'maxAge' => 600 ],
  // a request from a listed origin gets the Access-Control-* headers and a preflight
  // OPTIONS request is answered 204 before the application runs.

  $padCors = [];

  // Feature flags (lib/feature.php): name => TRUE, FALSE, a share between 0 and 1 of the
  // visitors (the same visitor always gets the same answer), or the name of a function
  // that decides. padFeature ( 'name' ) and {feature 'name'} read them.

  $padFeatures = [];

  // Remember-me (lib/auth.php): the name of an application function that finds a user's
  // row by its id - padLogin ( $user, TRUE ) needs it; the cookie is checked against the
  // row it answers, so a new password ends every remember-me cookie made before it.

  $padAuthRemember = '';

  // $padGzip compresses a web answer for a client that accepts gzip (lib/output.php); the
  // page cache has its own $padCacheServerGzip.
  //
  // $padCookies sends PAD's two identity cookies (inits/cookies.php): padSesID, the visitor
  // across requests, and padReqID, which the next request reports as its parent. FALSE sends
  // none - no tracking of the visitor's requests.
  //
  // $padNoNo makes the application plain PHP (inits/nono.php, the nono application): the
  // page's .php runs by itself and ends the request - no template, no level loop, no
  // exits - with every pad* global unset first; the page sends its own output and headers.

  $padGzip      = FALSE;
  $padCookies   = TRUE;
  $padNoNo      = FALSE;

?>

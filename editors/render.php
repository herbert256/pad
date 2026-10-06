<?php

  // Renders one page of any PAD application on the command line, for the editor tooling:
  // the language server and the MCP server check a template by having PAD itself render it,
  // and read what went wrong from the engine rather than guessing at it.
  //
  //   php editors/render.php <app> [<page>] [--apps=<dir>] [--source=<file>|-]
  //                          [--query=<a=1&b=2>] [--trace] [--host=<base>]
  //
  // The page's output goes to stdout. The status is the engine's own for the command line -
  // 0 when the request ended 2xx or 3xx, 1 otherwise - and a PAD error prints the JSON body
  // of pad/error/claude.php (the message, the PHP file and line, the engine's globals), so
  // a caller gets a strict syntax check of a page with its real data, no server needed.
  // A wrong application name ends with status 2 and a line on stderr; a page the application
  // does not have is the engine's own answer, "Page 'x' not found" on stdout with status 1.
  //
  // apps/cli/pad renders the cli application only; this one takes the application from its
  // first argument, nested ones too (regression/errors). --apps points at another
  // applications directory - the editors' test fixture under editors/fixture/apps - while
  // the engine and DATA stay this checkout's.
  //
  // --source renders the template text of a file, or of stdin with -, in place of the
  // page's own: $padPageSource, which inits/page.php and build/page.php honour. The page's
  // .php still runs, and the page need not exist - a template not saved yet, or one written
  // in no file at all, is checked in the application it belongs to. --query fills $_GET, as
  // a ?page&a=1 request would. --trace turns the execution trace on for this one request;
  // it is written under DATA/trace/<page>/. --host (or $PAD_HOST) is the base the web
  // server serves the applications under, http://localhost/pad/: the request is made to
  // look as if it came there, so $padHost and every URL the engine builds from it - a
  // {get} or a curl of its own pages - point at the real server, not at http://localhost/.
  //
  // The error action is set to boot whatever the application chose, through the engine's
  // own $padSetConfig queue: an error always comes back as the JSON body, never as a page.
  // The page's .php sees the GET request it would get from a browser - a page that asks
  // $_SERVER ['REQUEST_METHOD'] whether a form was posted found no method at all.

  $padHome = dirname ( __DIR__ );
  $padApps = "$padHome/apps/";
  $padData = "$padHome/DATA/";

  $padSetConfig = [ 'ErrorAction' => 'boot' ];

  $_SERVER ['REQUEST_METHOD'] = $_SERVER ['REQUEST_METHOD'] ?? 'GET';

  $renderArgs = [];

  foreach ( array_slice ( $argv, 1 ) as $renderArg )
    if ( str_starts_with ( $renderArg, '--apps=' ) )
      $padApps = rtrim ( substr ( $renderArg, 7 ), '/' ) . '/';
    elseif ( str_starts_with ( $renderArg, '--source=' ) )
      $padPageSource = (string) file_get_contents ( substr ( $renderArg, 9 ) == '-' ? 'php://stdin' : substr ( $renderArg, 9 ) );
    elseif ( str_starts_with ( $renderArg, '--query=' ) ) {
      parse_str ( substr ( $renderArg, 8 ), $_GET );
      $_REQUEST = $_GET;
    } elseif ( $renderArg == '--trace' )
      $padSetConfig ['Info'] = 'trace';
    elseif ( str_starts_with ( $renderArg, '--host=' ) )
      $renderHost = substr ( $renderArg, 7 );
    else
      $renderArgs [] = $renderArg;

  $renderHost = $renderHost ?? getenv ( 'PAD_HOST' );

  if ( $renderHost and ( $renderUrl = parse_url ( $renderHost ) ) and isset ( $renderUrl ['host'] ) ) {
    $_SERVER ['REQUEST_SCHEME'] = $renderUrl ['scheme'] ?? 'http';
    $_SERVER ['SERVER_PORT']    = $renderUrl ['port'] ?? ( $_SERVER ['REQUEST_SCHEME'] == 'https' ? 443 : 80 );
    $_SERVER ['HTTP_HOST']      = $renderUrl ['host'] . ( isset ( $renderUrl ['port'] ) ? ':' . $renderUrl ['port'] : '' );
    $padRoot                    = $renderUrl ['path'] ?? '/';
    if ( $_SERVER ['REQUEST_SCHEME'] == 'https' )
      $_SERVER ['HTTPS'] = 'on';
  }

  $padApp  = trim ( $renderArgs [0] ?? '', '/' );
  $padPage = trim ( $renderArgs [1] ?? 'index', '/' );

  // The engine dies with a bare text and status 0 for an application it cannot find - which
  // a caller would read as a page that rendered. The name is held to what a URL could carry,
  // so it never climbs out of the applications directory.

  if ( ! preg_match ( '/^[A-Za-z0-9][A-Za-z0-9_\/-]*$/D', $padApp )
       or str_contains ( $padApp, '//' )
       or ! is_dir ( $padApps . $padApp ) ) {
    fwrite ( STDERR, "render: no application '$padApp' in $padApps\n" );
    exit ( 2 );
  }

  unset ( $renderArgs, $renderArg, $renderHost, $renderUrl );

  include "$padHome/pad/pad.php";

?>

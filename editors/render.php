<?php

  // Renders one page of any PAD application on the command line, for the editor tooling:
  // the language server checks a template by having PAD itself render it, and reads what
  // went wrong from the engine rather than guessing at it.
  //
  //   php editors/render.php <app> [<page>] [--apps=<dir>]
  //
  // The page's output goes to stdout. The status is the engine's own for the command line -
  // 0 when the request ended 2xx or 3xx, 1 otherwise - and a PAD error prints the JSON body
  // of pad/error/claude.php (the message, the PHP file and line, the engine's globals), so
  // a caller gets a strict syntax check of a page with its real data, no server needed.
  // A wrong application or page name ends with status 2 and a line on stderr.
  //
  // apps/cli/pad renders the cli application only; this one takes the application from its
  // first argument, nested ones too (regression/errors). --apps points at another
  // applications directory - the editors' test fixture under editors/fixture/apps - while
  // the engine and DATA stay this checkout's.

  $padHome = dirname ( __DIR__ );
  $padApps = "$padHome/apps/";
  $padData = "$padHome/DATA/";

  $renderArgs = [];

  foreach ( array_slice ( $argv, 1 ) as $renderArg )
    if ( str_starts_with ( $renderArg, '--apps=' ) )
      $padApps = rtrim ( substr ( $renderArg, 7 ), '/' ) . '/';
    else
      $renderArgs [] = $renderArg;

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

  unset ( $renderArgs, $renderArg );

  include "$padHome/pad/pad.php";

?>

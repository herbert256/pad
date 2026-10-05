<?php

  // pad render <app> [page] [name=value ...]: one page of any application, rendered here
  // and written to stdout - the engine run in this process, as the cli application is, with
  // the page and its request values set the way a URL would set them.
  //
  // A failed page answers what every command-line request answers: the JSON error report
  // (with its template position) and exit status 1. PAD_HOST, when set, is the server the
  // page's own links and fetches point at ($padHostBase, and $padRoot its mount). PAD_LINT, set
  // by pad lint, puts the strict check on whatever the application chose; PAD_EXPORT, set by
  // pad export, asks for the page exactly as the web gets it; PAD_TEST, set by pad test,
  // runs a test page.

  $padRenderApp  = $argv [2] ?? '';
  $padRenderPage = $argv [3] ?? 'index';

  if ( ! cliApp ( $padRenderApp ) )
    return cliFail ( "there is no application named '$padRenderApp' - pad render <app> [page]" );

  if ( str_contains ( $padRenderPage, '=' ) ) {
    $padRenderArgs = array_slice ( $argv, 3 );
    $padRenderPage = 'index';
  } else
    $padRenderArgs = array_slice ( $argv, 4 );

  $_GET = [];

  foreach ( $padRenderArgs as $padRenderArg ) {
    list ( $padRenderKey, $padRenderValue ) = array_pad ( explode ( '=', $padRenderArg, 2 ), 2, '' );
    $_GET [$padRenderKey] = $padRenderValue;
  }

  $_REQUEST = $_GET;

  // A GET, as the URL would have been - pages that test the method find one.

  $_SERVER ['REQUEST_METHOD'] = 'GET';

  // inits/page.php reads the first argument as the page when nothing else names one; the
  // page is named here, so the arguments go.

  $_SERVER ['argv'] = [ $_SERVER ['argv'] [0] ?? 'pad' ];
  $_SERVER ['argc'] = 1;

  // The settings below go through the queue inits/configSet.php applies after the config
  // files, each added to it: a plain global set here is overwritten by config/config.php,
  // which is how PAD_HOST was ignored - its $padHostBase came back '' and every SELF://
  // fetch went to http://localhost/.

  $padSetConfig = [];

  // Its path is the mount, $padRoot - what $padGo, a page's own ?page links, are built on,
  // the way editors/render.php reads its --host. Without it a page rendered for a server
  // mounted under /pad/ linked to /<app>/?page, and pad export left those links unrewritten.

  if ( getenv ( 'PAD_HOST' ) ) {
    $padSetConfig ['HostBase'] = getenv ( 'PAD_HOST' );
    $padRoot = rtrim ( '/' . trim ( (string) parse_url ( getenv ( 'PAD_HOST' ), PHP_URL_PATH ), '/' ), '/' ) . '/';
  }

  if ( getenv ( 'PAD_LINT' ) )
    $padSetConfig ['CheckSyntax'] = TRUE;

  // pad export wants the page as the web gets it - the web output type, tidy and all, where
  // the command line otherwise writes it as console text - and nothing a developer's own
  // machine adds to a page: no toolbar, no live reload.

  if ( getenv ( 'PAD_EXPORT' ) )
    $padSetConfig = array_merge ( $padSetConfig, [ 'OutputType' => 'web', 'Toolbar' => FALSE, 'Reload' => FALSE ] );

  // pad test renders the application's _tests/ pages, bare like the suites' pages, with
  // every {assert} checked, and reads the status the request ended with from stderr - the
  // process status says only whether it went well.

  if ( getenv ( 'PAD_TEST' ) ) {

    $padTestRun           = TRUE;
    $padSetConfig         = array_merge ( $padSetConfig, [ 'Assert' => TRUE, 'Toolbar' => FALSE, 'Reload' => FALSE ] );
    $_GET ['padInclude']  = '';
    $_REQUEST             = $_GET;

    register_shutdown_function ( function () {
      fwrite ( STDERR, "\nPAD-STATUS " . ( $GLOBALS ['padExitStop'] ?? 500 ) . "\n" );
    } );

  }

  $padApp  = $padRenderApp;
  $padPage = $padRenderPage;
  $padApps = cliHome () . '/apps/';
  $padData = cliHome () . '/DATA/';

  unset ( $padRenderApp, $padRenderPage, $padRenderArgs, $padRenderArg, $padRenderKey, $padRenderValue );

  include cliHome () . '/pad/pad.php';

  return 0;

?>
